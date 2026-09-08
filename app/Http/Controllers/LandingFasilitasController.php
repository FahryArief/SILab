<?php

namespace App\Http\Controllers;

use App\Models\LandingFasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LandingFasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = LandingFasilitas::latest()->get();
        return view('admin.landing.fasilitas', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.webp';
            $path = public_path('images/landing/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scaleDown(width: 800);
            $image->toWebp(85)->save($path . $filename);

            $data['gambar'] = $filename;
        }

        LandingFasilitas::create($data);
        return back()->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fasilitas = LandingFasilitas::findOrFail($id);
        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($fasilitas->gambar && File::exists(public_path('images/landing/' . $fasilitas->gambar))) {
                File::delete(public_path('images/landing/' . $fasilitas->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.webp';
            $path = public_path('images/landing/');
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scaleDown(width: 800);
            $image->toWebp(85)->save($path . $filename);

            $data['gambar'] = $filename;
        }

        $fasilitas->update($data);
        return back()->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $fasilitas = LandingFasilitas::findOrFail($id);
        if ($fasilitas->gambar && File::exists(public_path('images/landing/' . $fasilitas->gambar))) {
            File::delete(public_path('images/landing/' . $fasilitas->gambar));
        }
        $fasilitas->delete();
        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}
