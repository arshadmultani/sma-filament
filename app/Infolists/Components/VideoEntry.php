<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\View\ComponentAttributeBag;

class VideoEntry extends Entry
{
    protected string $view = 'infolists.components.video-entry';

    protected bool $isMuted = false;
    protected bool $isDisablePictureInPicture = false;
    protected bool $hasControls = true;
    protected bool $isControlsListNoDownload = false;

    public function muted(bool $muted = true): static
    {
        $this->isMuted = $muted;

        return $this;
    }

    public function disablePictureInPicture(bool $disablePictureInPicture = true): static
    {
        $this->isDisablePictureInPicture = $disablePictureInPicture;

        return $this;
    }

    public function controls(bool $controls = true): static
    {
        $this->hasControls = $controls;

        return $this;
    }

    public function controlsListNoDownload(bool $controlsListNoDownload = true): static
    {
        $this->isControlsListNoDownload = $controlsListNoDownload;

        return $this;
    }

    public function isMuted(): bool
    {
        return $this->isMuted;
    }

    public function isDisablePictureInPicture(): bool
    {
        return $this->isDisablePictureInPicture;
    }

    public function hasControls(): bool
    {
        return $this->hasControls;
    }

    public function isControlsListNoDownload(): bool
    {
        return $this->isControlsListNoDownload;
    }

    public function getVideoAttributes(): ComponentAttributeBag
    {
        $attributes = new ComponentAttributeBag();

        if ($this->hasControls()) {
            $attributes = $attributes->merge(['controls' => true]);
        }

        if ($this->isMuted()) {
            $attributes = $attributes->merge(['muted' => true]);
        }

        if ($this->isControlsListNoDownload()) {
            $attributes = $attributes->merge(['controlsList' => 'nodownload']);
        }

        if ($this->isDisablePictureInPicture()) {
            $attributes = $attributes->merge(['disablePictureInPicture' => true]);
        }

        return $attributes;
    }

    public function getState(): array|string
    {
        $state = parent::getState();

        if (is_string($state)) {
            return [$state];
        }

        return $state;
    }
}
