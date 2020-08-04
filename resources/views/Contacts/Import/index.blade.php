@extends('layouts.app')

@section('content')
<file-upload
    upload_text="{{__('import_contacts.import_csv')}}"
    import_csv_text="{{__('import_contacts.upload')}}"
    :contact_fields="{{json_encode(App\Contact::CONTACT_FIELDS)}}"></file-upload>
@endsection
