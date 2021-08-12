@extends('admin.layouts.app')

@section('content')
    <h2 class="mb-4">{{ $page_title }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form>
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="mb-0 small text-muted" for="select-sent-status">Sent Status</label>
                        <select class="form-control" id="select-sent-status" name="sent_status">
                            <option value="">All</option>
                            <option value="sent" {{ request('sent_status')==='sent' ? 'selected' : '' }}>Sent</option>
                            <option value="not_sent" {{ request('sent_status')==='not_sent' ? 'selected' : '' }}>Not sent</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="mb-0 small text-muted" for="select-limit">Limit</label>
                        <select class="form-control" id="select-limit" name="limit">
                            <option value="">10</option>
                            <option value="50" {{ request('limit')==='50' ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('limit')==='100' ? 'selected' : '' }}>100</option>
                            <option value="250" {{ request('limit')==='250' ? 'selected' : '' }}>250</option>
                            <option value="500" {{ request('limit')==='500' ? 'selected' : '' }}>500</option>
                            <option value="1000" {{ request('limit')==='1000' ? 'selected' : '' }}>1,000</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="mb-0 small text-white d-block">Actions</label>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a class="btn btn-secondary my-1" href="{{ route('notification-logs') }}">Clear</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="card mb-4">
        <div class="table-responsive">
            <table id="table-notification-logs" class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Notification</th>
                        <th>Subject</th>
                        <th>Recipient(s)</th>
                        <th class="text-center">Queued</th>
                        <th class="text-center">Sent</th>
                        <th>Sent Time</th>
                        <th class="text-center">Tries</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notificationLogs AS $notificationLog)
                        <tr>
                            <td>{{ $notificationLog->id }}</td>
                            <td>{{ $notificationLog->mailable_name_string }}</td>
                            <td>{{ $notificationLog->subject }}</td>
                            <td>{{ implode(', ', $notificationLog->recipients) }}</td>
                            <td class="text-center text-muted">{{ $notificationLog->is_queued ? 'Yes' : 'No' }}</td>
                            <td class="text-center">
                                <i class="fas fa-{{ $notificationLog->is_sent ? 'check' : 'times' }} text-{{ $notificationLog->is_sent ? 'success' : 'danger' }}"></i>
                            </td>
                            <td>{{ $notificationLog->sent_at ? $notificationLog->sent_at->format('Y-m-d g:i A') : '' }}</td>
                            <td class="text-center">{{ $notificationLog->tries }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <nav>
        {{ $notificationLogs->appends([
            'sent_status' => request('sent_status'),
            'limit' => request('limit'),
            'page' => request('page'),
        ])->links() }}
    </nav>

@endsection
