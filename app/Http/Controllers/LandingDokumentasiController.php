<?php

namespace App\Http\Controllers;

use App\Models\LandingDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LandingDokumentasiController extends Controller
{
    public function index()
    {
        $dokumentasi = LandingDokumentasi::latest()->get();
        return view('admin.landing.dokumentasi', compact('dokumentasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'tag' => 'required|string|max:255',
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

        LandingDokumentasi::create($data);
        return back()->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'tag' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $dokumentasi = LandingDokumentasi::findOrFail($id);
        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($dokumentasi->gambar && File::exists(public_path('images/landing/' . $dokumentasi->gambar))) {
                File::delete(public_path('images/landing/' . $dokumentasi->gambar));
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

        $dokumentasi->update($data);
        return back()->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumentasi = LandingDokumentasi::findOrFail($id);
        if ($dokumentasi->gambar && File::exists(public_path('images/landing/' . $dokumentasi->gambar))) {
            File::delete(public_path('images/landing/' . $dokumentasi->gambar));
        }
        $dokumentasi->delete();
        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
