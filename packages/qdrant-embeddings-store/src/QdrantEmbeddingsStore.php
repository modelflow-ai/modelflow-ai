<?php

declare(strict_types=1);

/*
 * This file is part of the Modelflow AI package.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ModelflowAi\Embeddings\Store\Qdrant;

use ModelflowAi\Embeddings\Model\EmbeddingInterface;
use ModelflowAi\Embeddings\Store\EmbeddingsStoreInterface;
use Qdrant\Exception\InvalidArgumentException;
use Qdrant\Models\Filter\Condition\MatchString;
use Qdrant\Models\Filter\Filter;
use Qdrant\Models\PointsStruct;
use Qdrant\Models\PointStruct;
use Qdrant\Models\Request\CreateCollection;
use Qdrant\Models\Request\SearchRequest;
use Qdrant\Models\Request\VectorParams;
use Qdrant\Models\VectorStruct;
use Qdrant\Qdrant;
use Ramsey\Uuid\Uuid;

class QdrantEmbeddingsStore implements EmbeddingsStoreInterface
{
    public function __construct(
        private readonly Qdrant $client,
        private readonly string $collectionName,
    ) {
    }

    protected function createCollection(int $embeddingLength): void
    {
        $createCollection = new CreateCollection();

        $createCollection->addVector(
            new VectorParams(
                $embeddingLength,
                VectorParams::DISTANCE_COSINE,
            ),
        );

        $this->client->collections($this->collectionName)->create($createCollection);
    }

    protected function checkCollection(): bool
    {
        try {
            $this->client->collections($this->collectionName)->info();

            return true;
        } catch (InvalidArgumentException $exception) {
            if (404 === $exception->getCode()) {
                return false;
            }

            throw $exception;
        }
    }

    public function addDocument(EmbeddingInterface $embedding): void
    {
        if (!\is_array($embedding->getVector())) {
            throw new \Exception('Impossible to save a document without its vectors.');
        }

        if (!$this->checkCollection()) {
            $this->createCollection(\count($embedding->getVector()));
        }

        $points = new PointsStruct();
        $this->createPointFromDocument($points, $embedding);
        $this->client->collections($this->collectionName)->points()->upsert($points);
    }

    public function addDocuments(array $embeddings): void
    {
        $points = new PointsStruct();

        if ([] === $embeddings) {
            return;
        }

        if (!\is_array($embeddings[0]->getVector())) {
            throw new \Exception('Impossible to save a document without its vectors.');
        }

        if (!$this->checkCollection()) {
            $this->createCollection(\count($embeddings[0]->getVector()));
        }

        foreach ($embeddings as $embedding) {
            $this->createPointFromDocument($points, $embedding);
        }

        $this->client->collections($this->collectionName)->points()->upsert($points);
    }

    public function similaritySearch(array $vector, int $k = 4, array $additionalArguments = []): array
    {
        $vectorStruct = new VectorStruct($vector);
        $filter = new Filter();

        foreach ($additionalArguments as $key => $value) {
            $filter->addMust(new MatchString($key, (string) $value));
        }

        $searchRequest = (new SearchRequest($vectorStruct))
            ->setFilter($filter)
            ->setLimit($k)
            ->setParams([
                'hnsw_ef' => 128,
                'exact' => true,
            ])
            ->setWithPayload(true);

        $response = $this->client->collections($this->collectionName)->points()->search($searchRequest);
        $arrayResponse = $response->__toArray();
        $results = $arrayResponse['result'];

        if ((\is_countable($results) ? \count($results) : 0) === 0) {
            return [];
        }

        $embeddings = [];
        foreach ($results as $point) {
            $embeddings[] = $point['payload']['className']::fromArray($point['payload']);
        }

        return $embeddings;
    }

    private function createPointFromDocument(PointsStruct $points, EmbeddingInterface $embedding): void
    {
        if (!\is_array($embedding->getVector())) {
            throw new \Exception('Impossible to save a document without its vectors.');
        }

        $id = $this->formatUUIDFromUniqueId($embedding->getIdentifier());

        $payload = $embedding->toArray();
        $payload['id'] = $id;
        $payload['className'] = $embedding::class;

        $points->addPoint(
            new PointStruct(
                $id,
                new VectorStruct($embedding->getVector()),
                $payload,
            ),
        );
    }

    protected function formatUUIDFromUniqueId(string $identifier): string
    {
        // 1. Generate a SHA-256 hash of the data.
        $hash = \hash('sha256', $identifier);

        // 2. Extract portions of the hash to form the UUID.
        $part1 = \substr($hash, 0, 8);
        $part2 = \substr($hash, 8, 4);

        // For parts 3 and 4, we're making adjustments to ensure the UUID is a valid version 5 UUID.
        $part3 = (\hexdec(\substr($hash, 12, 4)) & 0x0FFF) | 0x5000;
        $part4 = (\hexdec(\substr($hash, 16, 4)) & 0x3FFF) | 0x8000;

        $part5 = \substr($hash, 20, 12);

        // 3. Combine the parts to form the UUID.
        return \sprintf('%08s-%04s-%04x-%04x-%12s', $part1, $part2, $part3, $part4, $part5);
    }
}
