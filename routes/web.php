<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Stories\Create as StoriesCreate;
use App\Livewire\Stories\Edit as StoriesEdit;
use App\Livewire\Stories\Index as StoriesIndex;
use App\Livewire\Stories\Show as StoriesShow;
use App\Livewire\StoryCovers\Create as StoryCoversCreate;
use App\Livewire\StoryCovers\Edit as StoryCoversEdit;
use App\Livewire\StoryCovers\Index as StoryCoversIndex;
use App\Livewire\StoryCovers\Show as StoryCoversShow;
use App\Livewire\StoryCustomMadeImages\Create as CustomMadeImagesCreate;
use App\Livewire\StoryCustomMadeImages\Edit as CustomMadeImagesEdit;
use App\Livewire\StoryCustomMadeImages\Index as CustomMadeImagesIndex;
use App\Livewire\StoryCustomMadeImages\Show as CustomMadeImagesShow;
use App\Livewire\StoryCustomMades\Create as StoryCustomMadesCreate;
use App\Livewire\StoryCustomMades\Edit as StoryCustomMadesEdit;
use App\Livewire\StoryCustomMades\Index as StoryCustomMadesIndex;
use App\Livewire\StoryCustomMades\Show as StoryCustomMadesShow;
use App\Livewire\StoryPages\Create as StoryPagesCreate;
use App\Livewire\StoryPages\Edit as StoryPagesEdit;
use App\Livewire\StoryPages\Index as StoryPagesIndex;
use App\Livewire\StoryPages\Show as StoryPagesShow;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('custom-stories', \App\Livewire\CustomStories\Index::class)->name('custom-stories.index');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('stories', StoriesIndex::class)->name('stories.index');
    Route::get('stories/create', StoriesCreate::class)->name('stories.create');
    Route::get('stories/{story}', StoriesShow::class)->name('stories.show');
    Route::get('stories/{story}/edit', StoriesEdit::class)->name('stories.edit');

    Route::get('stories/{story}/pages', StoryPagesIndex::class)->name('story-pages.index');
    Route::get('stories/{story}/pages/create', StoryPagesCreate::class)->name('story-pages.create');
    Route::get('stories/{story}/pages/{pagePrompt}', StoryPagesShow::class)->name('story-pages.show');
    Route::get('stories/{story}/pages/{pagePrompt}/edit', StoryPagesEdit::class)->name('story-pages.edit');

    Route::get('stories/{story}/covers', StoryCoversIndex::class)->name('story-covers.index');
    Route::get('stories/{story}/covers/create', StoryCoversCreate::class)->name('story-covers.create');
    Route::get('stories/{story}/covers/{coverPrompt}', StoryCoversShow::class)->name('story-covers.show');
    Route::get('stories/{story}/covers/{coverPrompt}/edit', StoryCoversEdit::class)->name('story-covers.edit');

    Route::get('stories/{story}/custom-mades', StoryCustomMadesIndex::class)->name('story-custom-mades.index');
    Route::get('stories/{story}/custom-mades/create', StoryCustomMadesCreate::class)->name('story-custom-mades.create');
    Route::get('stories/{story}/custom-mades/{customMade}', StoryCustomMadesShow::class)->name('story-custom-mades.show');
    Route::get('stories/{story}/custom-mades/{customMade}/edit', StoryCustomMadesEdit::class)->name('story-custom-mades.edit');

    Route::get('stories/{story}/custom-mades/{customMade}/images', CustomMadeImagesIndex::class)->name('custom-made-images.index');
    Route::get('stories/{story}/custom-mades/{customMade}/images/create', CustomMadeImagesCreate::class)->name('custom-made-images.create');
    Route::get('stories/{story}/custom-mades/{customMade}/images/{image}', CustomMadeImagesShow::class)->name('custom-made-images.show');
    Route::get('stories/{story}/custom-mades/{customMade}/images/{image}/edit', CustomMadeImagesEdit::class)->name('custom-made-images.edit');
});
