@extends('layouts.app')
@section('content')
<x-user.config-form :user="Auth::user()" />
@endsection