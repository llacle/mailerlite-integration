@extends('layouts.master')

@section('title', 'Welcome')

@section('content')
  @empty($apikey_object)
      <div class="alert alert-danger" role="alert">
          Please set an API key first.
      </div>
  @endempty

  <main role="main">
      <div class="jumbotron">
        <div class="col-sm-8 mx-auto">
          <h1>Welcome!</h1>
          <p>This is a small application to manage MailerLite subscribers.</p>

          @empty($apikey_object)
              <p>Please add an API key to get started.</p>
              <p><a class="btn btn-primary" href="{{ url('/apikey') }}" role="button">API Key »</a></p>
          @endempty
        </div>
      </div>
    </main>
@stop
