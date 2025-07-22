<x-filament::page>
    <form wire:submit.prevent="generate" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit" color="primary">
            Generate Tagihan
        </x-filament::button>
    </form>
</x-filament::page>
