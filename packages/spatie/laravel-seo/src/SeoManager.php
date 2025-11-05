<?php

namespace Spatie\LaravelSeo;

use Illuminate\Contracts\Support\Htmlable;

class SeoManager implements Htmlable
{
    protected array $meta = [];

    public function __construct(protected array $config = [])
    {
        $this->meta = [
            'title' => $config['default_title'] ?? config('app.name'),
            'description' => $config['default_description'] ?? '',
            'keywords' => $config['default_keywords'] ?? '',
            'canonical' => $config['canonical'] ?? url('/')
        ];
    }

    public function title(?string $title): static
    {
        if ($title) {
            $this->meta['title'] = $title;
        }

        return $this;
    }

    public function description(?string $description): static
    {
        if ($description) {
            $this->meta['description'] = $description;
        }

        return $this;
    }

    public function keywords(string|array|null $keywords): static
    {
        if (is_array($keywords)) {
            $keywords = implode(', ', array_filter($keywords));
        }

        if ($keywords) {
            $this->meta['keywords'] = $keywords;
        }

        return $this;
    }

    public function canonical(?string $url): static
    {
        if ($url) {
            $this->meta['canonical'] = $url;
        }

        return $this;
    }

    public function meta(string $name, string $content): static
    {
        $this->meta['custom'][$name] = $content;

        return $this;
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        $tags = [];

        if (! empty($this->meta['title'])) {
            $tags[] = '<title>'.e($this->meta['title']).'</title>';
        }

        if (! empty($this->meta['description'])) {
            $tags[] = '<meta name="description" content="'.e($this->meta['description']).'">';
        }

        if (! empty($this->meta['keywords'])) {
            $tags[] = '<meta name="keywords" content="'.e($this->meta['keywords']).'">';
        }

        if (! empty($this->meta['canonical'])) {
            $tags[] = '<link rel="canonical" href="'.e($this->meta['canonical']).'">';
        }

        if (! empty($this->meta['custom'])) {
            foreach ($this->meta['custom'] as $name => $content) {
                $tags[] = '<meta name="'.e($name).'" content="'.e($content).'">';
            }
        }

        return implode(PHP_EOL, $tags);
    }
}
