#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\ArrayLoader;

// Templates
$packageModuleTemplate = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<module type="JAVA_MODULE" version="4">
  <component name="NewModuleRootManager" inherit-compiler-output="true">
    <exclude-output />
    <content url="file://\$MODULE_DIR\$">
      <sourceFolder url="file://\$MODULE_DIR\$/src" isTestSource="false" {% if src_namespace %}packagePrefix="{{ src_namespace }}" {% endif %}/>
      <sourceFolder url="file://\$MODULE_DIR\$/tests" isTestSource="true" {% if src_namespace %}packagePrefix="{{ test_namespace }}" {% endif %}/>
      <excludeFolder url="file://\$MODULE_DIR\$/vendor" />
    </content>
    <orderEntry type="inheritedJdk" />
    <orderEntry type="sourceFolder" forTests="false" />
  </component>
</module>
EOT;

$projectModuleTemplate = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<module type="JAVA_MODULE" version="4">
  <component name="NewModuleRootManager" inherit-compiler-output="true">
    <exclude-output />
    <content url="file://\$MODULE_DIR\$">
      {% for package in packages -%}
      <excludeFolder url="file://\$MODULE_DIR\${{ package.package_path }}/vendor" />
      {% endfor %}

      {% for package in packages %}{% if package.type in ['packages', 'integrations'] -%}
      <excludeFolder url="file://\$MODULE_DIR\$/vendor/modelflow-ai/{{ package.package_name }}" />
      {% endif %}{% endfor %}

    </content>
    <orderEntry type="inheritedJdk" />
    <orderEntry type="sourceFolder" forTests="false" />
  </component>
</module>
EOT;

$modulesTemplate = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
  <component name="ProjectModuleManager">
    <modules>
      <module fileurl="file://\$PROJECT_DIR\$/.idea/modelflow-ai.iml" filepath="\$PROJECT_DIR\$/.idea/modelflow-ai.iml" />
      {% for package in packages -%}
      <module fileurl="file://{{ package.root_path }}/.idea/{{ package.type }}-{{ package.package_name }}.iml" filepath="{{ package.root_path }}/.idea/{{ package.type }}-{{ package.package_name }}.iml" />
      {% endfor %}

    </modules>
  </component>
</project>
EOT;

// Set up Twig
$loader = new ArrayLoader([
    'package-module.iml.twig' => $packageModuleTemplate,
    'module.iml.twig' => $projectModuleTemplate,
    'modules.xml.twig' => $modulesTemplate,
]);
$twig = new Environment($loader);

// Function to extract namespaces from composer.json
function extractNamespaces($composerJsonPath) {
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    $srcNamespace = $composerJson['autoload']['psr-4'] ?? null;
    $testNamespace = $composerJson['autoload-dev']['psr-4'] ?? null;

    return [
        'src_namespace' => $srcNamespace ? key($srcNamespace) : null,
        'test_namespace' => $testNamespace ? key($testNamespace) : null,
    ];
}

// Glob all composer.json files
$composerFiles = glob(getcwd() . '/*/*/composer.json', GLOB_BRACE);

$packages = [];
foreach ($composerFiles as $composerFile) {
    $rootPath = str_replace(getcwd(), '$PROJECT_DIR$', dirname($composerFile));
    $packagePath = str_replace(getcwd(), '', dirname($composerFile));
    $namespaces = extractNamespaces($composerFile);
    $packageName = basename(dirname($composerFile));
    $type = basename(dirname($composerFile, 2));

    if ($packageName === 'symfony') {
        $packageName = 'symfony-bundle';
    }

    $packages[] = $package = [
        'root_path' => $rootPath,
        'package_path' => $packagePath,
        'src_namespace' => $namespaces['src_namespace'],
        'test_namespace' => $namespaces['test_namespace'],
        'package_name' => $packageName,
        'type' => $type,
    ];

    $moduleContent = $twig->render('package-module.iml.twig', $package);
    $moduleIdeaDir = dirname($composerFile) . '/.idea';
    @mkdir($moduleIdeaDir, 0777, true);
    file_put_contents($moduleIdeaDir . '/' . $type . '-' . $packageName . '.iml', $moduleContent);
}

$projectIdeaDir = getcwd() . '/.idea';
@mkdir($projectIdeaDir, 0777, true);

$projectModuleContent = $twig->render('module.iml.twig', ['packages' => $packages]);
file_put_contents(getcwd() . '/.idea/modelflow-ai.iml', $projectModuleContent);

$modulesContent = $twig->render('modules.xml.twig', ['packages' => $packages]);
file_put_contents($projectIdeaDir . '/modules.xml', $modulesContent);
