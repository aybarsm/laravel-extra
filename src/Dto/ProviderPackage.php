<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;
use Aybarsm\Extra\Concerns\HasMetaData;
use Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage\HasBasePath;
use Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage\HasComposer;
use Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage\HasEssentials;
use Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage\HasProviderDetails;
use Aybarsm\Laravel\Extra\Custom\Spatie\LaravelPackageTools\Concerns;
use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;
use Illuminate\Support\Str;

final class ProviderPackage
{
    use HasMetaData;
    use HasEssentials;
    use HasProviderDetails;
    use HasBasePath;
    use HasComposer;

    public function __construct(string $author, string $name, ?string $version = null)
    {
        self::validateEssentials($author, $name);
        $this->author = $author;
        $this->name = $name;
        self::setMetaData("package.{$this->author}.{$this->name}", true);
        if (!is_null($version)) $this->version = $version;
    }

    public function __destruct()
    {
        foreach(['package', 'composer'] as $prefix){
            self::unsetMetaData("{$prefix}.{$this->author}.{$this->name}");
        }
    }

    private static function validateEssentials(string $author, string $name): void
    {
        throw_if(
            blank($author) or blank($name),
            ProviderPackageException::class,
            sprintf('Author and name are required for `%s`', static::class),
        );

        $pattern = '/^[a-zA-Z][a-zA-Z0-9-_]+$/';
        throw_if(
            !Str::isMatch($pattern, $author) || !Str::isMatch($pattern, $name),
            ProviderPackageException::class,
            sprintf('Author and name must match `%s` naming convention for `%s`', $pattern,  static::class),
        );

        throw_if(
            static::getMetaData("package.{$author}.{$name}") === true,
            ProviderPackageException::class,
            sprintf('ProviderPackage of `%s` already registered for %s',  "{$author}/{$name}", static::class),
        );
    }

    public function getComposerJson(int $depth = 512, int $flags = 0): ?array
    {
        if (!$this->hasComposer()) return null;

        $content = self::getMetaData("composer.{$this->author}.{$this->name}");

        if (blank($content)) {
            $content = file_get_contents($this->getComposerPath());
            self::setMetaData("composer.{$this->author}.{$this->name}", $content);
        }

        return json_decode(
            json: $content,
            associative: true,
            depth: $depth,
            flags: $flags
        );
    }

//    public function getBasePath(): string
//    {
//        return $this->basePath ?? $this->getProviderDir();
//    }
//
//    public function basePath(?string $directory = null): string
//    {
//        if (blank($directory)) {
//            return $this->getBasePath();
//        }
//
//        return fs_path($this->getBasePath(), $directory);
//    }

//    public static function make(mixed $value): static
//    {
//        $discovered = new DiscoveredValue($value);
//        $package = new static();
//
//        if ($discovered->isSubOf(ServiceProvider::class)) {
//            $package->setBasePath(dirname(new \ReflectionClass($value)->getFileName()));
//            $discovered->value = ;
//            dump([
//                'discovered' => $discovered,
//            ]);
//        }
//
//        return $package;
//    }
}
