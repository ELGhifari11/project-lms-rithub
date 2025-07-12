<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; text-align: center;">
    <div>
        <small >PENDING</small>
        <div style="font-size: 1.50rem; color: #ffc107;">{{ $data['PENDING'] }}</div>
    </div>

    <div>
        <small >PROCESSING</small>
        <div style="font-size: 1.50rem; color: #17a2b8;">{{ $data['PROCESSING'] }}</div>
    </div>
    <div>
        <small >COMPLETED</small>
        <div style="font-size: 1.50rem; color: #28a745;">{{ $data['COMPLETED'] }}</div>
    </div>
    <div>
        <small >FAILED</small>
        <div style="font-size: 1.50rem; color: #dc3545;">{{ $data['FAILED'] }}</div>
    </div>
</div>
