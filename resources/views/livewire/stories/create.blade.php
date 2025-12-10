<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" :href="route('stories.index')" wire:navigate>
            <flux:icon name="arrow-left" class="h-4 w-4" />
        </flux:button>
        <flux:heading size="xl">{{ __('Create Story') }}</flux:heading>
    </div>

    <div class="mx-auto w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="idea"
                :label="__('Story Idea')"
                :placeholder="__('A magical adventure about...')"
                required
            />

            <flux:textarea
                wire:model="description"
                :label="__('Description')"
                :placeholder="__('Describe the story in detail...')"
                rows="4"
                required
            />

            <flux:input
                wire:model="moral_lesson"
                :label="__('Moral Lesson')"
                :placeholder="__('The lesson this story teaches...')"
                required
            />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="language" :label="__('Language')">
                    <option value="Arabic">{{ __('Arabic') }}</option>
                    <option value="English">{{ __('English') }}</option>
                    <option value="French">{{ __('French') }}</option>
                    <option value="Spanish">{{ __('Spanish') }}</option>
                    <option value="German">{{ __('German') }}</option>
                </flux:select>

                <flux:input
                    wire:model="pages_count"
                    :label="__('Number of Pages')"
                    type="number"
                    min="1"
                    max="50"
                    required
                />
            </div>

            <flux:textarea
                wire:model="images_input"
                :label="__('Cover Image URLs')"
                :placeholder="__('Enter image URLs separated by commas...')"
                rows="2"
            />
            <flux:text class="text-xs text-zinc-500">
                {{ __('Enter multiple image URLs separated by commas. These will be used as cover images.') }}
            </flux:text>

            <flux:checkbox
                wire:model="is_active"
                :label="__('Active')"
                :description="__('Make this story available for use')"
            />

            <div class="flex items-center justify-end gap-4 pt-4">
                <flux:button variant="ghost" :href="route('stories.index')" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Create Story') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
