<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerQBpg1yc\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerQBpg1yc/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerQBpg1yc.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerQBpg1yc\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerQBpg1yc\srcApp_KernelDevDebugContainer([
    'container.build_hash' => 'QBpg1yc',
    'container.build_id' => '80dc5123',
    'container.build_time' => 1564996830,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerQBpg1yc');
