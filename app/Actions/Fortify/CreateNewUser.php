<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'inviteCode' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $invite = DB::table('invites')
            ->where('invites.invite_code', $input['inviteCode'])
            ->first();

        if($invite == null) {
            throw ValidationException::withMessages(['email' => 'Invalid invite code']);
        }

        if($invite->status == 'Utilizado') {
            throw ValidationException::withMessages(['email' => 'Invite code already used']);
        }
        
        $affected_user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $affected = DB::table('invites')
            ->where('id', $invite->id)
            ->update(['status' => 'Utilizado', 'used_by_user' => $affected_user->id]);
        
        return $affected_user;
    }
}
