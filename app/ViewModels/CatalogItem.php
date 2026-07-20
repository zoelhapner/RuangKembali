<?php
namespace App\ViewModels;

use ArrayAccess;

class CatalogItem implements ArrayAccess
{
    protected array $attrs;

    public function __construct(array $attrs = [])
    {
        $this->attrs = $attrs;
    }

    // Dipanggil oleh beberapa paket / blade untuk mendapatkan PK
    public function getKey()
    {
        return $this->attrs['id'] ?? null;
    }

    // Magic getter -> $item->name
    public function __get($key)
    {
        return $this->attrs[$key] ?? null;
    }

    // Magic isset
    public function __isset($key)
    {
        return isset($this->attrs[$key]);
    }

    // ArrayAccess: $item['name']
    public function offsetExists($offset)
    {
        return isset($this->attrs[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->attrs[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->attrs[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attrs[$offset]);
    }

    // Jika perlu array
    public function toArray(): array
    {
        return $this->attrs;
    }
}
