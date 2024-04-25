<br/>
<div align="center">
 <img alt="Anthropic Logo" src="https://avatars.githubusercontent.com/u/152068817?s=768&amp;v=4" width="200" height="200">
</div>

<h1 align="center">
Modelflow AI<br/>
Anthropic<br/>
<br/>
</h1>

<br/>

<div align="center">
The Anthropic package is a comprehensive API client for the Anthropic AI model. It provides a simple and intuitive
interface to interact with the Anthropic AI model, allowing you to create chat conversations.
</div>

<br/>

> **Note**:
> This is part of the `modelflow-ai` project create issues in the [main repository](https://github.com/modelflow-ai/.github).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

<br/>

## Installation

To install the Anthropic package, you need to have PHP 8.2 or higher and Composer installed on your machine. Then, you
can add the package to your project by running the following command:

```bash
composer require modelflow-ai/anthropic
```

## Examples

Here are some examples of how you can use the Mistral package in your PHP applications. You can find more detailed
examples in the [examples directory](examples).

## Usage

### Creating a Client

First, you need to create a client. The client is the main entry point to interact with the Anthropic AI model. You can
create a client using the `Anthropic` class:

```php
use ModelflowAi\Anthropic\Anthropic;

$client = Anthropic::client('your-api-key');
```

### Using the Chat Resource

The Chat resource allows you to create chat conversations and get chat completions.

```php
$chat = $client->chat();

// Create a chat conversation
$parameters = [
    'model' => Model::CLAUDE_3_OPUS,
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant.'
        ],
        [
            'role' => 'user',
            'content' => 'Who won the world series in 2020?'
        ]
    ]
];
$response = $chat->create($parameters);

// The response is an instance of CreateResponse
echo $response->content[0]->text;
```

## API Documentation

For more detailed information about the Anthropic API, please refer to
the [official API documentation](https://docs.anthropic.com/claude/reference/getting-started-with-the-api).

## Open Points

### Tools

The messages resource does not support tools until now.

### Text completions

The Text Completions resource is not yet in this package.

### Embeddings

The Embeddings resource is not yet in the Anthropic API. As soon as it is available, we will provide a resource to
generate and manipulate embeddings for your data.

## Testing

To run the tests, use PHPUnit:

```bash
composer test
```

## Contributing

Contributions are welcome. Please open an issue or submit a pull request in the main repository
at [https://github.com/modelflow-ai/modelflow-ai](https://github.com/modelflow-ai/modelflow-ai).

## License

This project is licensed under the MIT License. For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
