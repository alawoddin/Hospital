<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineStock;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PharmacyController extends Controller
{
    public function PharmacyDashboard() {
        $medicineStock = MedicineStock::sum('quantity');
        $expiringMedicines = MedicineStock::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->count();
        $pendingPrescriptions = Prescription::where('pharmacy_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        return view('backend.pharmacy.index', compact('medicineStock', 'expiringMedicines', 'pendingPrescriptions'));
    }


    //Logout Route
    public function PharmacyLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // End Logout

    // Pharmacy Profile
    public function PharmacyProfile(){
        $pharmacy = Auth::user();
        return view('backend.pharmacy.profile.pharmacy_profile', compact('pharmacy'));
    }
    // End Pharmacy Profile

    // Update Profile
    public function UpdatePharmacyProfile(Request $request) {

        $pharmacy = Auth::user();

       if ($request->file('photo')) {

            if ($pharmacy->photo && file_exists(public_path($pharmacy->photo))) {
                unlink(public_path($pharmacy->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/pharmacy/profile/'.$name_gen));
            $save_url = 'upload/pharmacy/profile/'.$name_gen;

            $pharmacy->photo = $save_url;
        }

        $pharmacy->name = $request->name;
        $pharmacy->phone = $request->phone;
        $pharmacy->address = $request->address;
        $pharmacy->role = $request->role;

        $pharmacy->save();
        return redirect()->route('pharmacy.profile');
    }
    // End Update Profile

    // Pharmacy Patients
    public function PharmacyPatients(){
        $pharmacyId = Auth::id();
        $prescriptions = Prescription::with('doctor', 'patient', 'items')->where('pharmacy_id', $pharmacyId)->get();
        return view('backend.pharmacy.patients.index', compact('prescriptions'));
    }
    // End Pharmacy Patients

    // Prescription Details
    public function PrescriptionDetails($id){
        $prescription = Prescription::with(['patient', 'doctor', 'items'])->findOrFail($id);
        return view('backend.pharmacy.patients.details', compact('prescription'));
    }
    // End Prescription Details

    // All Company
    public function AllCompany(){
        $company = Company::all();
        return view('backend.pharmacy.company.index', compact('company'));
    }
    // End All Company
    
    // Add Company
    public function AddCompany() {
        return view('backend.pharmacy.company.add');
    }
    // End Add Company

    // Store Company
    public function StoreCompany(Request $request){
        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/pharmacy/company/'.$name_gen));
            $save_url = 'upload/pharmacy/company/'.$name_gen;

        Company::create([
            'user_id' => Auth::user()->id,
            'name'=> $request->name,
            'image'=>$save_url,
        ]);
        return redirect()->route('all.company');

        }else{
            Company::create([
            'user_id' => Auth::user()->id,
            'name'=> $request->name,
            ]);
        }
        return redirect()->route('all.company');
    }
    // End Store Company

    // Edit Company
    public function EditCompany($id){
        $user = Company::findOrFail($id);
        return view('backend.pharmacy.company.edit', compact('user'));
    }
    // End Edit Company

    // Store Company
    public function UpdateCompany(Request $request) {
        $user_id = $request->id;
        $user = Company::findOrFail($user_id);
        if ($request->file('image')) {

            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(500,500)->save(public_path('upload/pharmacy/company/'.$name_gen));
            $save_url = 'upload/pharmacy/company/'.$name_gen;

        Company::find($user_id)->update([
            'user_id' => Auth::user()->id,
            'name'=> $request->name,
            'image'=>$save_url,
        ]);
        return redirect()->route('all.company');

        }else{
            Company::findOrFail($user_id)->update([
            'user_id' => Auth::user()->id,
            'name'=> $request->name,
            ]);
        }
        return redirect()->route('all.company');
    }
    // End Update Company

    // Delete Company
    public function DeleteCompany($id) {
        $user = Company::findOrFail($id);
        if ($user->image && file_exists(public_path($user->image))) {
            unlink(public_path($user->image));
        }
        $user = Company::findOrFail($id)->delete();
        return redirect()->back();
    }
    // End Delete Company

    // All Supplier
    public function AllSupplier() {
        $supplier = Supplier::all();
        return view('backend.pharmacy.supplier.index', compact('supplier'));
    }
    // End All Supplier

    // Add Supplier
    public function AddSupplier() {
        return view('backend.pharmacy.supplier.add');
    }
    // End Add Supplier

    // Store Supplier
    public function StoreSupplier(Request $request) {
        
        $request->validate([
        'name' => ['required', 'string', 'max:200'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['required', 'string', 'max:20'],
        'address' => ['required', 'string'],
    ]);

        Supplier::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'phone'=> $request->phone,
            'address'=> $request->address,
        ]);
        return redirect()->route('all.supplier');
    }
    // End Store Supplier

    // Edit Supplier
    public function EditSupplier($id) {
        $supplier = Supplier::find($id);
        return view('backend.pharmacy.supplier.edit', compact('supplier'));
    }
    // End Edit Supplier

    // Update Supplier
    public function UpdateSupplier(Request $request) {

        $supplier = $request->id;
        $supplier_id = Supplier::findOrFail($supplier);

        $request->validate([
        'name' => ['required', 'string', 'max:200'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['required', 'string', 'max:20'],
        'address' => ['required', 'string'],
    ]);

        Supplier::find($supplier)->update([
            'name'=> $request->name,
            'email'=> $request->email,
            'phone'=> $request->phone,
            'address'=> $request->address,
        ]);
        return redirect()->route('all.supplier');
    }
    // End Update Supplier

    // Delete Supplier
    public function DeleteSupplier($id) {
        $supplier = Supplier::findOrFail($id);
        $supplier = Supplier::findOrFail($id)->delete();
        return redirect()->back();
    }

    // All Products 
    public function AllProducts() {
        $product = Product::all();
        $suppliers = Supplier::all();
        return view('backend.pharmacy.products.index', compact('product','suppliers'));
    }
    // End All Products

    // Add Products 
    public function AddProducts() {
        $suppliers = Supplier::all();
        return view('backend.pharmacy.products.add', compact('suppliers'));
    }
// End Add Products

    // Store Products
    public function StoreProducts(Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        Product::create([
            'name'=> $request->name,
            'category'=> $request->category,
            'description'=> $request->description,
            'quantity'=> $request->quantity,
            'price'=> $request->price,
            'expiry_date'=> $request->expiry_date,
            'supplier_id'=> $request->supplier_id,
        ]);
        return redirect()->route('all.products');
    }
    // End Store Products

    // Edit Products
    public function EditProducts($id) {
        $product = Product::findOrFail($id);
        return view('backend.pharmacy.products.edit', compact('product'));
    }
    // End Edit Products

    // Update Products
    public function UpdateProducts(Request $request) {
        $product_id = $request->id;
        $product = Product::findOrFail($product_id);

        Product::find($product_id)->update([
            'name'=> $request->name,
            'category'=> $request->category,
            'description'=> $request->description,
            'quantity'=> $request->quantity,
            'price'=> $request->price,
            'expiry_date'=> $request->expiry_date,
            'supplier_id'=> $request->supplier_id,
        ]);
        return redirect()->route('all.products');
    }
    // End Update Products

    // Delete Products
    public function DeleteProducts($id) {
        $product = Product::findOrFail($id);
        $product = Product::findOrFail($id)->delete();
        return redirect()->back();
    }
    // End Delete Products

    // All Expires Medicine
    public function AllExpiresMedicine(){
        $expiredProducts = Product::where('expiry_date', '<', now())->get();
        return view('backend.pharmacy.expires_medicine.index', compact('expiredProducts'));
    }
    // End All Expires Medicine

    // Edit Expires Medicine
    public function EditExpiresMedicine($id){
        $product = Product::findOrFail($id);
        return view('backend.pharmacy.expires_medicine.edit', compact('product'));
    }
    // End Edit Expires Medicine

    // Update Expires Medicine
    public function UpdateExpiresMedicine(Request $request){
        $product_id = $request->id;
        $product = Product::findOrFail($product_id);

        Product::find($product_id)->update([
            'quantity'=> $request->quantity,
            'price'=> $request->price,
            'expiry_date'=> $request->expiry_date,
        ]);
        return redirect()->route('all.expires.medicine');
    }

    public function AllMedicines()
    {
        $medicines = Medicine::with('category', 'stocks')->latest()->get();

        return view('backend.pharmacy.medicines.index', compact('medicines'));
    }

    public function AddMedicine()
    {
        $categories = MedicineCategory::orderBy('name')->get();

        return view('backend.pharmacy.medicines.add', compact('categories'));
    }

    public function StoreMedicine(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'medicine_category_id' => ['nullable', 'exists:medicine_categories,id'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'batch_no' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request) {
            $medicine = Medicine::create($request->only([
                'name', 'medicine_category_id', 'generic_name', 'description', 'unit_price', 'unit',
            ]));

            if ($request->filled('quantity')) {
                MedicineStock::create([
                    'medicine_id' => $medicine->id,
                    'batch_no' => $request->batch_no,
                    'quantity' => $request->quantity,
                    'expiry_date' => $request->expiry_date,
                    'purchase_price' => $request->unit_price,
                ]);
            }
        });

        return redirect()->route('pharmacy.medicines')->with('success', 'Medicine added.');
    }

    public function EditMedicine($id)
    {
        $medicine = Medicine::with('stocks')->findOrFail($id);
        $categories = MedicineCategory::orderBy('name')->get();

        return view('backend.pharmacy.medicines.edit', compact('medicine', 'categories'));
    }

    public function UpdateMedicine(Request $request)
    {
        $medicine = Medicine::findOrFail($request->id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'medicine_category_id' => ['nullable', 'exists:medicine_categories,id'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $medicine->update([
            'name' => $request->name,
            'medicine_category_id' => $request->medicine_category_id,
            'generic_name' => $request->generic_name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
            'unit' => $request->unit ?? $medicine->unit,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('pharmacy.medicines')->with('success', 'Medicine updated.');
    }

    public function AddMedicineStock($id)
    {
        $medicine = Medicine::findOrFail($id);

        return view('backend.pharmacy.medicines.stock', compact('medicine'));
    }

    public function StoreMedicineStock(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'batch_no' => ['nullable', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'expiry_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        MedicineStock::create([
            'medicine_id' => $medicine->id,
            'batch_no' => $request->batch_no,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'purchase_price' => $request->purchase_price,
        ]);

        return redirect()->route('pharmacy.medicines')->with('success', 'Stock added.');
    }

    public function AllMedicineCategories()
    {
        $categories = MedicineCategory::withCount('medicines')->get();

        return view('backend.pharmacy.categories.index', compact('categories'));
    }

    public function StoreMedicineCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:medicine_categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        MedicineCategory::create($request->only('name', 'description'));

        return back()->with('success', 'Category created.');
    }

    public function DispensePrescription(Request $request, $id)
    {
        $prescription = Prescription::with('items')->where('pharmacy_id', Auth::id())->findOrFail($id);
        $this->authorize('dispense', $prescription);

        DB::transaction(function () use ($prescription) {
            foreach ($prescription->items as $item) {
                if ($item->dispensed) {
                    continue;
                }

                if ($item->medicine_id) {
                    $remaining = $item->quantity;
                    $stocks = MedicineStock::where('medicine_id', $item->medicine_id)
                        ->where('quantity', '>', 0)
                        ->orderBy('expiry_date')
                        ->lockForUpdate()
                        ->get();

                    foreach ($stocks as $stock) {
                        if ($remaining <= 0) {
                            break;
                        }

                        $deduct = min($remaining, $stock->quantity);
                        $stock->decrement('quantity', $deduct);
                        $remaining -= $deduct;
                    }

                    if ($remaining > 0) {
                        throw new \RuntimeException('Insufficient stock for '.$item->medicine);
                    }
                }

                $item->update(['dispensed' => true]);
            }

            $allDispensed = $prescription->items()->where('dispensed', false)->doesntExist();
            $prescription->update([
                'status' => $allDispensed ? 'dispensed' : 'partially_dispensed',
            ]);
        });

        return back()->with('success', 'Prescription dispensed and stock updated.');
    }

    public function PharmacyReports()
    {
        $lowStock = Medicine::with('stocks')->get()->filter(fn ($m) => $m->totalStock() <= 10);
        $expiring = MedicineStock::with('medicine')->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->get();
        $dispensedCount = Prescription::where('pharmacy_id', Auth::id())->where('status', 'dispensed')->count();

        return view('backend.pharmacy.reports.index', compact('lowStock', 'expiring', 'dispensedCount'));
    }

}
