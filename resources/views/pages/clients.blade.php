<?php

use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

name('clients');

middleware(['auth', 'verified']);

new class extends Component {
    use WithPagination;

    public ClientForm $form;

    #[Computed]
    public function clients()
    {
        return auth()->user()->currentTeam->clients()->latest()->paginate(25);
    }

    public function save()
    {
        $this->form->store();

        Flux::modal('create-client')->close();
        $this->form->reset();
    }

    public function edit(Client $client)
    {
        $this->form->setClient($client);

        Flux::modal('edit-client')->show();
    }

    public function update()
    {
        $this->form->update();

        Flux::modal('edit-client')->close();
    }

    public function delete(Client $client)
    {
        $client->delete();
    }
}; ?>

<x-app-layout>
    @volt('pages.clients')
        <div>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="max-sm:w-full sm:flex-1">
                    <flux:heading size="xl">{{ __('Clients') }}</flux:heading>
                    <flux:subheading>{{ __('Manage your clients.') }}</flux:subheading>
                </div>
                <div>
                    <flux:modal.trigger name="create-client">
                        <flux:button variant="primary">{{ __('Add client') }}</flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            @if ($this->clients->isNotEmpty())
                <flux:table id="clients" class="mt-8">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Name') }}</flux:table.column>
                        <flux:table.column>{{ __('Email') }}</flux:table.column>
                        <flux:table.column>{{ __('Phone') }}</flux:table.column>
                        <flux:table.column>{{ __('Instagram') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->clients as $client)
                            <flux:table.row :key="$client->id">
                                <flux:table.cell>{{ $client->first_name }} {{ $client->last_name }}</flux:table.cell>
                                <flux:table.cell>{{ $client->email }}</flux:table.cell>
                                <flux:table.cell>{{ $client->phone }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($client->instagram)
                                        <a href="{{ $client->instagram }}" target="_blank" class="text-blue-500 hover:underline">{{ $client->instagram }}</a>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex gap-2 justify-end">
                                        <form wire:submit="edit({{ $client->id }})">
                                            <flux:button type="submit" variant="ghost" size="sm" icon="pencil" inset="top bottom"></flux:button>
                                        </form>
                                        <form wire:submit="delete({{ $client->id }})" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            <flux:button type="submit" variant="ghost" size="sm" icon="trash" inset="top bottom"></flux:button>
                                        </form>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

                <div class="mt-6">
                    <flux:pagination :paginator="$this->clients" />
                </div>
            @else
                <div class="mt-14 flex flex-1 flex-col items-center justify-center pb-32">
                    <flux:icon.users class="mb-6 size-12 text-zinc-500 dark:text-white/70" />
                    <flux:heading size="lg" level="2">{{ __('No clients') }}</flux:heading>
                    <flux:subheading class="mb-6 max-w-72 text-center">
                        {{ __('We couldn’t find any clients. Create one to get started.') }}
                    </flux:subheading>
                    <flux:modal.trigger name="create-client">
                        <flux:button variant="primary">{{ __('Add client') }}</flux:button>
                    </flux:modal.trigger>
                </div>
            @endif

            <flux:modal name="create-client" class="w-full sm:max-w-lg">
                <form wire:submit="save" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Add a new client') }}</flux:heading>
                        <flux:subheading>{{ __('Enter the client details.') }}</flux:subheading>
                    </div>

                    <flux:input wire:model="form.first_name" :label="__('First name')" type="text" />
                    <flux:input wire:model="form.last_name" :label="__('Last name')" type="text" />
                    <flux:input wire:model="form.email" :label="__('Email')" type="email" />
                    <flux:input wire:model="form.phone" :label="__('Phone')" type="text" />
                    <flux:input wire:model="form.instagram" :label="__('Instagram')" type="text" />

                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                    </div>
                </form>
            </flux:modal>

            <flux:modal name="edit-client" class="w-full sm:max-w-lg">
                <form wire:submit="update" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Edit client') }}</flux:heading>
                        <flux:subheading>{{ __('Update the client details.') }}</flux:subheading>
                    </div>

                    <flux:input wire:model="form.first_name" :label="__('First name')" type="text" />
                    <flux:input wire:model="form.last_name" :label="__('Last name')" type="text" />
                    <flux:input wire:model="form.email" :label="__('Email')" type="email" />
                    <flux:input wire:model="form.phone" :label="__('Phone')" type="text" />
                    <flux:input wire:model="form.instagram" :label="__('Instagram')" type="text" />

                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        </div>
    @endvolt
</x-app-layout>
