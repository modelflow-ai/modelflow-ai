<br/>
<div align="center">
 <img alt="Anthropic Adapter Logo" src="https://avatars.githubusercontent.com/u/152068817?s=768&amp;v=4" width="200" height="200">
</div>

<h1 align="center">
Modelflow AI<br/>
Anthropic Adapter<br/>
<br/>
</h1>

<br/>

<div align="center">
The adapter integrates Anthropic AI models into Modelflow  AI.
</div>

<br/>

> **Note**:
> This is part of the `modelflow-ai` project create issues in the [main repository](https://github.com/modelflow-ai/.github).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

<br/>

## Installation

To install the Anthropic Adapter package, you need to have PHP 8.2 or higher and Composer installed on your machine.
Then, you can add the package to your project by running the following command:

```bash
composer require modelflow-ai/anthropic-adapter
```

## Examples

Here are some examples of how you can use the Anthropic Adapter in your PHP applications. You can find more detailed
examples in the [examples directory](examples).

## Usage

First, initialize the client:

```php
use ModelflowAi\Anthropic\Anthropic;

$client = Anthropic::client('your-api-key');
```

Then, you can use the `AnthropicChatModelAdapter`:

```php
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\AIChatRequestHandler;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\AnthropicAdapter\Chat\AnthropicChatAdapter;
use ModelflowAi\PromptTemplate\ChatPromptTemplate;

$modelAdapter = new AnthropicChatAdapter($client, Model::CLAUDE_3_HAIKU);

/** @var DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree([
    new DecisionRule($modelAdapter, [CapabilityCriteria::SMART]),
]);
$handler = new AIChatRequestHandler($decisionTree);

$response = $handler->createRequest(
    ...ChatPromptTemplate::create(
        new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, 'You are an {feeling} bot'),
        new AIChatMessage(AIChatMessageRoleEnum::USER, 'Hello {where}!'),
    )->format(['where' => 'world', 'feeling' => 'angry']),
)
    ->addCriteria(CapabilityCriteria::SMART)
    ->build()
    ->execute();

echo \sprintf('%s: %s', $response->getMessage()->role->value, $response->getMessage()->content);
```

## Contributing

Contributions are welcome. Please open an issue or submit a pull request in the main repository
at [https://github.com/modelflow-ai/.github](https://github.com/modelflow-ai/.github).

## License

This project is licensed under the MIT License. For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
