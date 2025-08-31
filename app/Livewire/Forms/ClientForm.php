<?php

namespace App\Livewire\Forms;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Livewire\Form;

class ClientForm extends Form
{
    public ?Client $client = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $instagram = null;

    protected function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:255'],
            'instagram'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        $this->first_name = $client->first_name;
        $this->last_name = $client->last_name;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->instagram = $client->instagram;
    }

    public function store()
    {
        $this->validate();

        return Auth::user()->currentTeam->clients()->create([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'instagram'  => $this->instagram,
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->client->update([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'instagram'  => $this->instagram,
        ]);
    }
}
