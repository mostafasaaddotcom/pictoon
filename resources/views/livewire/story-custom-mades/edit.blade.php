<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" :href="route('story-custom-mades.index', $story)" wire:navigate>
            <flux:icon name="arrow-left" class="h-4 w-4" />
        </flux:button>
        <div>
            <flux:heading size="xl">{{ __('Edit Custom Story') }}</flux:heading>
            <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
        </div>
    </div>

    <x-action-message class="me-3" on="custom-made-updated">
        {{ __('Custom story updated.') }}
    </x-action-message>

    <div class="mx-auto w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="child_name"
                :label="__('Child Name')"
                :placeholder="__('Enter the child\'s name...')"
                required
            />

            <flux:select wire:model="child_gender" :label="__('Gender')">
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
                <option value="other">{{ __('Other') }}</option>
            </flux:select>

            <flux:input
                wire:model="child_age"
                type="number"
                :label="__('Age')"
                min="1"
                max="18"
                required
            />

            <flux:input
                wire:model="child_image_url"
                type="url"
                :label="__('Child Image URL (optional)')"
                :placeholder="__('https://example.com/image.jpg')"
            />

            <flux:select wire:model="status" :label="__('Status')">
                <option value="pending">{{ __('Pending') }}</option>
                <option value="processing">{{ __('Processing') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
                <option value="failed">{{ __('Failed') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-4 pt-4">
                <flux:button variant="ghost" :href="route('story-custom-mades.index', $story)" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
