<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" :href="route('story-covers.index', $story)" wire:navigate>
            <flux:icon name="arrow-left" class="h-4 w-4" />
        </flux:button>
        <div>
            <flux:heading size="xl">{{ __('Add Cover Prompt') }}</flux:heading>
            <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
        </div>
    </div>

    <div class="mx-auto w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:select wire:model="type" :label="__('Cover Type')">
                <option value="front">{{ __('Front Cover') }}</option>
                <option value="back">{{ __('Back Cover') }}</option>
            </flux:select>

            <flux:textarea
                wire:model="image_prompt"
                :label="__('Image Prompt')"
                :placeholder="__('Describe the cover image to be generated...')"
                rows="6"
                required
            />

            <div class="flex items-center justify-end gap-4 pt-4">
                <flux:button variant="ghost" :href="route('story-covers.index', $story)" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Add Cover') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
