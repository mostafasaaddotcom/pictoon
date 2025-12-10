<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('story-custom-mades.show', [$story, $customMade])" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Custom Made Images') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $customMade->child_name }}</flux:text>
            </div>
        </div>
        <flux:button variant="primary" :href="route('custom-made-images.create', [$story, $customMade])" wire:navigate>
            {{ __('Add Image') }}
        </flux:button>
    </div>

    <x-action-message class="me-3" on="image-deleted">
        {{ __('Image deleted.') }}
    </x-action-message>

    @if ($images->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
            <div class="text-center">
                <flux:icon name="photo" class="mx-auto h-12 w-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">{{ __('No images yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Add images for this custom story.') }}</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('custom-made-images.create', [$story, $customMade])" wire:navigate>
                        {{ __('Add First Image') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($images as $image)
                <div wire:key="image-{{ $image->id }}" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex flex-col gap-3">
                        @if ($image->image_url)
                            <img src="{{ $image->image_url }}" alt="Page {{ $image->page_number }}" width="250" height="250" class="rounded-lg object-cover">
                        @else
                            <div class="flex h-32 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                <flux:icon name="photo" class="h-8 w-8 text-zinc-400" />
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <flux:badge color="{{ match($image->image_type) {
                                    'page' => 'blue',
                                    'cover_front' => 'green',
                                    'cover_back' => 'purple',
                                    default => 'zinc'
                                } }}">
                                    {{ match($image->image_type) {
                                        'page' => __('Page :num', ['num' => $image->page_number]),
                                        'cover_front' => __('Front Cover'),
                                        'cover_back' => __('Back Cover'),
                                        default => $image->image_type
                                    } }}
                                </flux:badge>
                                <flux:badge color="{{ match($image->status) {
                                    'pending' => 'yellow',
                                    'processing' => 'blue',
                                    'completed' => 'green',
                                    'failed' => 'red',
                                    default => 'zinc'
                                } }}">
                                    {{ ucfirst($image->status) }}
                                </flux:badge>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:button size="sm" :href="route('custom-made-images.show', [$story, $customMade, $image])" wire:navigate>
                                {{ __('View') }}
                            </flux:button>
                            <flux:button size="sm" :href="route('custom-made-images.edit', [$story, $customMade, $image])" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:modal.trigger name="delete-image-{{ $image->id }}">
                                <flux:button size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-image-{{ $image->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Delete Image') }}</flux:heading>
                                        <flux:text class="mt-2">
                                            {{ __('Are you sure you want to delete this image? This action cannot be undone.') }}
                                        </flux:text>
                                    </div>
                                    <div class="flex gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                        </flux:modal.close>
                                        <flux:button variant="danger" wire:click="delete({{ $image->id }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
