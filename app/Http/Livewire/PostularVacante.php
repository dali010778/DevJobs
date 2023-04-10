<?php

namespace App\Http\Livewire;

use App\Models\Vacante;
use App\Notifications\NuevoCandidato;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostularVacante extends Component
{
   public $cv;
   public $vacante;

   use WithFileUploads;

   protected $rules = [
    'cv' => ['required', 'mimes:pdf']
   ];

   public function mount(Vacante $vacante)
   {
        $this->vacante = $vacante;
   }

   public function postularme()
   {
    $datos = $this->validate();

    //Alamcenar el curriculum en el servidor

    $cv = $this->cv->store('public/cv');
    $datos['cv'] = str_replace('public/cv/', '', $cv);


     //Crear el candidato a la vacante
        $this->vacante->candidatos()->create([
            'user_id' => auth()->user()->id,
            'cv' => $datos['cv']
        ]);

     //Crear notificacion y envoar el email
        $this->vacante->reclutador->notify(new NuevoCandidato($this->vacante->id, 
            $this->vacante->titulo, auth()->user()->id
        ));

     //Mostrar al usuario un mensaje de ok
     session()->flash('mensaje', 'Se envio correctamente tu información, mucha suerte!');
     return redirect()->back();
   }
   
    public function render()
    {
        return view('livewire.postular-vacante');
    }
}
