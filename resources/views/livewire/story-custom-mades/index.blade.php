<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('stories.show', $story)" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Custom Made Stories') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
            </div>
        </div>
        <flux:button variant="primary" :href="route('story-custom-mades.create', $story)" wire:navigate>
            {{ __('Create Custom') }}
        </flux:button>
    </div>

    <x-action-message class="me-3" on="custom-made-deleted">
        {{ __('Custom story deleted.') }}
    </x-action-message>

    @if ($customMades->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
            <div class="text-center">
                <flux:icon name="user-circle" class="mx-auto h-12 w-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">{{ __('No custom stories yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Create personalized stories for children.') }}</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('story-custom-mades.create', $story)" wire:navigate>
                        {{ __('Create First Custom Story') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4">
            @foreach ($customMades as $customMade)
                <div wire:key="custom-made-{{ $customMade->id }}" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <flux:heading size="lg">{{ $customMade->child_name }}</flux:heading>
                                <flux:badge color="{{ match($customMade->status) {
                                    'pending' => 'yellow',
                                    'processing' => 'blue',
                                    'completed' => 'green',
                                    'failed' => 'red',
                                    default => 'zinc'
                                } }}">
                                    {{ ucfirst($customMade->status) }}
                                </flux:badge>
                            </div>
                            <flux:text class="mt-2">
                                {{ __(':age years old, :gender', ['age' => $customMade->child_age, 'gender' => ucfirst($customMade->child_gender)]) }}
                            </flux:text>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" :href="route('story-custom-mades.show', [$story, $customMade])" wire:navigate>
                                {{ __('View') }}
                            </flux:button>
                            <flux:button size="sm" :href="route('story-custom-mades.edit', [$story, $customMade])" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:modal.trigger name="delete-custom-made-{{ $customMade->id }}">
                                <flux:button size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-custom-made-{{ $customMade->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Delete Custom Story') }}</flux:heading>
                                        <flux:text class="mt-2">
                                            {{ __('Are you sure you want to delete the custom story for :name? This action cannot be undone.', ['name' => $customMade->child_name]) }}
                                        </flux:text>
                                    </div>
                                    <div class="flex gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                        </flux:modal.close>
                                        <flux:button variant="danger" wire:click="delete({{ $customMade->id }})">
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
