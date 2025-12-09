<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" :href="route('custom-made-images.index', [$story, $customMade])" wire:navigate>
            <flux:icon name="arrow-left" class="h-4 w-4" />
        </flux:button>
        <div>
            <flux:heading size="xl">{{ __('Edit Image') }}</flux:heading>
            <flux:text class="text-zinc-500">{{ $customMade->child_name }}</flux:text>
        </div>
    </div>

    <x-action-message class="me-3" on="image-updated">
        {{ __('Image updated.') }}
    </x-action-message>

    <div class="mx-auto w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:select wire:model="image_type" :label="__('Image Type')">
                <option value="page">{{ __('Page') }}</option>
                <option value="cover_front">{{ __('Front Cover') }}</option>
                <option value="cover_back">{{ __('Back Cover') }}</option>
            </flux:select>

            <flux:input
                wire:model="page_number"
                type="number"
                :label="__('Page Number')"
                min="0"
                required
            />

            <flux:input
                wire:model="reference_number"
                type="number"
                :label="__('Reference Number (optional)')"
                min="0"
            />

            <flux:input
                wire:model="image_url"
                type="url"
                :label="__('Image URL (optional)')"
                :placeholder="__('https://example.com/image.jpg')"
            />

            <flux:select wire:model="status" :label="__('Status')">
                <option value="pending">{{ __('Pending') }}</option>
                <option value="processing">{{ __('Processing') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
                <option value="failed">{{ __('Failed') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-4 pt-4">
                <flux:button variant="ghost" :href="route('custom-made-images.index', [$story, $customMade])" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
