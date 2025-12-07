<?php namespace Core;

function is_absolute_path(string $path): bool
{
    // Windows absolute paths (C:\..., D:/..., etc.)
    if (preg_match('#^[A-Za-z]:[\\\\/]#', $path)) {
        return true;
    }

    // UNC paths (\\Server\Share)
    if (str_starts_with($path, '\\\\')) {
        return true;
    }

    // Unix absolute paths (/home, /etc/..., etc.)
    if (str_starts_with($path, '/')) {
        return true;
    }

    return false;
}


class Path
{
    public array $segments;
    public function __construct(
        private string|Path|array $path
    )
    {
        if ($path instanceof Path)
        {
            $this->segments = $path->segments;
        }

        if (is_array($path))
        {
            $this->segments = [];
            foreach ($path as $segment)
            {
                if (str_contains($segment, DIRECTORY_SEPARATOR))
                {
                    array_merge($this->segments, explode($segment, DIRECTORY_SEPARATOR));
                }
                else
                {
                    $this->segments[] = $segment;
                }
            }
        }

        if (is_string($path))
        {
            $this->segments = explode($path, DIRECTORY_SEPARATOR);

        }

    }

    public function join(string ...$parts): self
    {
        $all = array_merge([$this->path], $parts);
        $clean = [];

        foreach ($all as $p) {
            $clean[] = trim($p, DIRECTORY_SEPARATOR);
        }

        return new self(implode(DIRECTORY_SEPARATOR, $clean));
    }

    public function dirname(): self
    {
        return new self(dirname($this->path));
    }

    public function basename(): string
    {
        return basename($this->path);
    }

    public function __toString(): string
    {
        return $this->path;
    }
}