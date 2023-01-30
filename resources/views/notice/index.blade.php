@extends('layouts/ecl')

@section('content')

    <div class="ecl-row">
        <div class="ecl-col-12">

            <div class="ecl-u-mt-l ecl-u-f-r">
                <x-ecl.cta-button label="Create a Notice" url="{{ route('notice.create') }}" />
            </div>

            <h1>Notices</h1>
            <ul>

                @foreach($notices as $notice)
                    <li>
                        <a href="{{ route('notice.show', [$notice]) }}">{{$notice->title}}</a>
                        @if($notice->entities)
                            <ul>
                                @foreach($notice->entities as $entity)
                                    <li>
                                        {{$entity->name}}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </li>
                @endforeach

                {{ $notices->links('paginator') }}
            </ul>
        </div>
    </div>

@endsection

