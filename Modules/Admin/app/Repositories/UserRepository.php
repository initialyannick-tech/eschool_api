<?php

namespace Modules\Admin\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Admin\Emails\NewUserMail;

use Modules\Admin\Models\User;
use Modules\Admin\Transformers\AuthResource;
use Modules\Admin\Transformers\UserResource;

class UserRepository
{

    /**
     * Liste des users sans pagination
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users =  User::orderBy('id', 'desc')->with(['role'])->get();
        return UserResource::collection($users);
    }

    public function paginate(): AnonymousResourceCollection
    {
        $users =  User::orderBy('id', 'desc')->with(['role'])->paginate(10);
        return UserResource::collection($users);
    }


    public function show($id)
    {
        return User::whereId($id)->with(['role'])->first();
    }


    public function store($data)
    {
        $password = $this->generatePassword();
        $data['password'] = $password;
        $user = new User;
        $user->fill($data);
        if($user->save()){
            Mail::to($user->email)->send(new NewUserMail($user, $password));
            return $user;
        }
        return false;
    }

    public function update($data, $id)
    {
        $user = User::whereId($id)->first();
        $user->fill($data);
        if($user->save()){
            return $user;
        }
        return false;
    }

    public function destroy($id): bool
    {
        $user = User::whereId($id)->first();
        if($user->delete()){
            return true;
        }
        return false;
    }


    private function generatePassword(): string
    {
        return Str::random(6);
    }


    public function updatePassword($data)
    {
        $user = User::whereId($data->user_id)->first();
        $user->password = $data->password;
        $user->password_changed = User::ACTIVE;
        if($user->save()){
            return AuthResource::make($user);
        }
        return false;
    }
}
