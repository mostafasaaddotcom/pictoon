<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" :href="route('story-pages.index', $story)" wire:navigate>
            <flux:icon name="arrow-left" class="h-4 w-4" />
        </flux:button>
        <div>
            <flux:heading size="xl">{{ __('Add Page Prompt') }}</flux:heading>
            <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
        </div>
    </div>

    <div class="mx-auto w-full max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <flux:input
                    wire:model="page_number"
                    :label="__('Page Number')"
                    type="number"
                    min="1"
                    required
                />

                <flux:select wire:model="art_style" :label="__('Art Style')">
                    <option value="watercolor">{{ __('Watercolor') }}</option>
                    <option value="cartoon">{{ __('Cartoon') }}</option>
                    <option value="realistic">{{ __('Realistic') }}</option>
                    <option value="anime">{{ __('Anime') }}</option>
                    <option value="oil painting">{{ __('Oil Painting') }}</option>
                    <option value="digital art">{{ __('Digital Art') }}</option>
                    <option value="pencil sketch">{{ __('Pencil Sketch') }}</option>
                </flux:select>
            </div>

            <flux:input
                wire:model="scene_title"
                :label="__('Scene Title')"
                :placeholder="__('A brief title for this scene...')"
                required
            />

            <flux:textarea
                wire:model="story_text"
                :label="__('Story Text')"
                :placeholder="__('The text that will appear on this page...')"
                rows="4"
                required
            />

            <flux:textarea
                wire:model="image_prompt"
                :label="__('Image Prompt')"
                :placeholder="__('Describe the image to be generated for this page...')"
                rows="4"
                required
            />

            <flux:input
                wire:model="emotions"
                :label="__('Emotions')"
                :placeholder="__('Happy, excited, curious...')"
            />
            <flux:text class="text-xs text-zinc-500">
                {{ __('Describe the emotions conveyed in this scene.') }}
            </flux:text>

            <div class="flex items-center justify-end gap-4 pt-4">
                <flux:button variant="ghost" :href="route('story-pages.index', $story)" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Add Page') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
