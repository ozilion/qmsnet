<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Denetciler;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagement extends Controller
{
  /**
   * Redirect to user-management view.
   *
   */
  public function UserManagement()
  {
    $users = User::all();
    $userCount = $users->count();
    $verified = User::whereNotNull('email_verified_at')->get()->count();
    $notVerified = User::whereNull('email_verified_at')->get()->count();
    $usersUnique = $users->unique(['email']);
    $userDuplicates = $users->diff($usersUnique)->count();

    return view('content.laravel-example.user-management', [
      'totalUser' => $userCount,
      'verified' => $verified,
      'notVerified' => $notVerified,
      'userDuplicates' => $userDuplicates,
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'email',
      4 => 'role',
      5 => 'email_verified_at',
      6 => 'status',
    ];

    $search = [];

    $totalData = User::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      $users = User::offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      $search = $request->input('search.value');

      $users = User::where('id', 'LIKE', "%{$search}%")
        ->orWhere('name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->orWhere('role', 'LIKE', "%{$search}%")
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = User::where('id', 'LIKE', "%{$search}%")
        ->orWhere('name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->orWhere('role', 'LIKE', "%{$search}%")
        ->count();
    }

    $data = [];

    if (!empty($users)) {
      // providing a dummy id instead of database ids
      $ids = $start;

      foreach ($users as $user) {

        $status = Denetciler::where(["uid" => $user->id])->first();

        $nestedData['id'] = $user->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $user->name;
        $nestedData['email'] = $user->email;
        $nestedData['role'] = $user->role;
        $nestedData['email_verified_at'] = $user->email_verified_at;
        $nestedData['status'] = $status->is_active ?? 0;

        $data[] = $nestedData;
      }
    }


    if ($data) {
      return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'code' => 200,
        'data' => $data,
      ]);
    } else {
      return response()->json([
        'message' => 'Internal Server Error',
        'code' => 500,
        'data' => [],
      ]);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store1(Request $request)
  {
    $userID = $request->id;

    if ($userID) {
      // update the value
      $users = User::updateOrCreate(
        ['id' => $userID],
        ['name' => $request->name, 'email' => $request->email, 'role' => $request->role]
      );

      // Örneğin, formdan gelen role adı:
      $roleName = $request->role;

      // Rolü bul veya oluştur:
      $role = Role::firstOrCreate(['name' => $roleName]);

      // Kullanıcıyı alın (örneğin, $user):
      $user = User::find($userID);

      // Kullanıcıya rolü ata:
      $user->assignRole($role);

      // user updated
      return response()->json('Updated');
    } else {
      // create new one if email is unique
      $userEmail = User::where('email', $request->email)->first();

      if (empty($userEmail)) {
        $users = User::updateOrCreate(
          ['id' => $userID],
          ['name' => $request->name, 'email' => $request->email, 'role' => $request->role, 'password' => bcrypt(Str::random(10))]
        );

        // Örneğin, formdan gelen role adı:
        $roleName = $request->role;

        // Rolü bul veya oluştur:
        $role = Role::firstOrCreate(['name' => $roleName]);

        // Kullanıcıyı alın (örneğin, $user):
        $user = User::find($userID);

        // Kullanıcıya rolü ata:
        $user->assignRole($role);

        // user created
        return response()->json('Created');
      } else {
        // user already exist
        return response()->json(['message' => "already exits"], 422);
      }
    }
  }

  public function store(Request $request)
  {
    // Temel validasyon kuralları
//    $validated = $request->validate([
//      'name'        => 'required|string|max:255',
//      'email'       => 'required|email|max:255',
//      'role'        => 'required|string',
//      // 'permissions' alanı isteğe bağlı; gelen veri dizi veya virgülle ayrılmış string olabilir.
//      'permissions' => 'nullable',
//    ]);

    // Kullanıcı var mı kontrolü: eğer "id" gönderilmişse güncelle, yoksa oluştur.
    if ($request->filled('id')) {
      $user = User::updateOrCreate(
        ['id' => $request->id],
        [
          'name'  => $request->name,
          'email' => $request->email,
          'role'  => $request->role,
        ]
      );
      $message = 'Updated';
    } else {
      $existing = User::where('email', $request->email)->first();
      if ($existing) {
        return response()->json(['message' => "already exists"], 422);
      }
      $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'role'     => $request->role,
        'password' => bcrypt(Str::random(10)),
      ]);
      $message = 'Created';
    }

    // Rolü bul veya oluştur, ve kullanıcıya atama (syncRoles, önceki rolleri kaldırır)
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $request->role]);
    $user->syncRoles([$role]);

    // Permissions ataması:
    if ($request->filled('permissions')) {
      $permissions = $request->input('permissions');

      // Gelen verinin array olup olmadığını kontrol edelim, değilse virgülle ayrılmış string'i array'e çevirelim
      if (!is_array($permissions)) {
        $permissions = explode(',', $permissions);
        $permissions = array_map('trim', $permissions);
      }

      // Mevcut izinleri alalım
      $existingPermissions = $user->getAllPermissions()->pluck('name')->toArray();

      // Eğer yeni izin seti mevcut izinlerle farklıysa, syncPermissions ile güncelleyelim.
      if (array_diff($permissions, $existingPermissions) || array_diff($existingPermissions, $permissions)) {
        $user->syncPermissions($permissions);
      }
    }

    return response()->json([
      'message' => $message,
      'user'    => $user,
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $where = ['id' => $id];

    $users = User::where($where)->first();

    // Kullanıcının bağlı olduğu şirketi alıyoruz.
    $company = Company::find($users->kurulusid);
    $users["kurulus"] = $company ? $company->adi : '';

    // Kullanıcının rol id'si üzerinden roles tablosundan rol ismini alıyoruz.
    $roles = Roles::all();
    $userrole = "";
    foreach ($roles as $role){
      $sec = $role->name == $users->role ? " selected" : "";
      $userrole .= '<option value="'. $role->name .'"'. $sec .'>'. $role->name .'</option>';
    }
    $users["role"] = $userrole;

    // Kullanıcının rol id'si üzerinden roles tablosundan rol ismini alıyoruz.
    $permissions = Permissions::all();
    $userpermission = "";
    foreach ($permissions as $permission){
      $sec = $permission->name == $users->role ? " selected" : "";
      $userpermission .= '<option value="'. $permission->name .'"'. $sec .'>'. $permission->name .'</option>';
    }
    $users["permissions"] = $userpermission;

//    print_r($roles);
    return response()->json($users);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $where = ['uid' => $id];
    $user = Denetciler::where($where)->first();
    if (!$user) {
      return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.'], 404);
    }
    $user->is_active = 0;
    $user->save();

    return response()->json(['success' => true, 'message' => 'Kullanıcı başarıyla devre dışı bırakıldı.']);
  }

}
