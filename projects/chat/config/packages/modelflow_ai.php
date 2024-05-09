<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $providers = [];
    $adapters = [];

    if (isset($_ENV['OLLAMA_URL'])) {
        $providers['ollama'] = [
            'enabled' => true,
            'url' => '%env(OLLAMA_URL)%',
        ];

        $adapters = array_merge($adapters, [
            'llama2' => [
                'enabled' => true,
            ],
            'nexusraven' => [
                'enabled' => true,
            ],
            'llava' => [
                'enabled' => true,
            ],
        ]);
    }

    if (isset($_ENV['OPENAI_API_KEY'])) {
        $providers['openai'] = [
            'enabled' => true,
            'credentials' => [
                'api_key' => '%env(OPENAI_API_KEY)%',
            ],
        ];

        $adapters = array_merge($adapters, [
            'gpt4' => [
                'enabled' => true,
            ],
            'gpt3.5' => [
                'enabled' => true,
            ],
            'dall_e_2' => [
                'enabled' => true,
            ],
        ]);
    }

    if (isset($_ENV['MISTRAL_API_KEY'])) {
        $providers['mistral'] = [
            'enabled' => true,
            'credentials' => [
                'api_key' => '%env(MISTRAL_API_KEY)%',
            ],
        ];

        $adapters = array_merge($adapters, [
            'mistral_tiny' => [
                'enabled' => true,
            ],
            'mistral_small' => [
                'enabled' => true,
            ],
            'mistral_medium' => [
                'enabled' => true,
            ],
            'mistral_large' => [
                'enabled' => true,
            ],
        ]);
    }

    if (isset($_ENV['ANTHROPIC_API_KEY'])) {
        $providers['anthropic'] = [
            'enabled' => true,
            'credentials' => [
                'api_key' => '%env(ANTHROPIC_API_KEY)%',
            ],
        ];

        $adapters = array_merge($adapters, [
            'claude_3_opus' => [
                'enabled' => true,
            ],
            'claude_3_sonnet' => [
                'enabled' => true,
            ],
            'claude_3_haiku' => [
                'enabled' => true,
            ],
        ]);
    }

    $container->extension('modelflow_ai', [
        'providers' => $providers,
        'adapters' => $adapters,
    ]);
};
