<?php

namespace App\Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Arrayable;
use Stringable;
use Throwable;

abstract class EditRecordRedirectToList extends EditRecord
{
    public string $initialDataHash = '';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->initialDataHash = $this->computeDataHash((array) ($this->data ?? []));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSaveFormAction(): Action
    {
        $action = parent::getSaveFormAction();

        return $action
            ->disabled(fn () => ! $this->hasFormChanges())
            ->tooltip(fn () => $this->hasFormChanges() ? null : 'Tidak ada perubahan untuk disimpan.');
    }

    protected function hasFormChanges(): bool
    {
        return $this->computeDataHash((array) ($this->data ?? [])) !== $this->initialDataHash;
    }

    protected function computeDataHash(array $data): string
    {
        $normalized = $this->normalizeForHash($data);

        try {
            return md5(json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        } catch (Throwable) {
            return md5(serialize($normalized));
        }
    }

    protected function normalizeForHash(mixed $value): mixed
    {
        if ($value === '') {
            return null;
        }

        if (is_array($value)) {
            $normalized = [];

            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeForHash($item);
            }

            return $normalized;
        }

        if (is_object($value)) {
            if ($value instanceof Arrayable) {
                return $this->normalizeForHash($value->toArray());
            }

            if ($value instanceof Stringable) {
                return $value::class . ':' . (string) $value;
            }

            return $value::class . ':#' . spl_object_id($value);
        }

        return $value;
    }
}
