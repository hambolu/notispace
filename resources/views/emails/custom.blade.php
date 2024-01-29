<!DOCTYPE html>
<html>
<head>
    <title>{{ $templateData['subject'] }}</title>
</head>
<body>
    <h1>{{ $templateData['subject'] }}</h1>
    <p>{{ $templateData['body'] }}</p>
    
    @if(isset($templateData['cta']) && !empty($templateData['cta']['name']) && !empty($templateData['cta']['url']))
        <p><a href="{{ $templateData['cta']['url'] }}">{{ $templateData['cta']['name'] }}</a></p>
    @endif

    <!-- Display attachments if available -->
    @if(isset($attachments) && count($attachments) > 0)
        <p><strong>Attachments:</strong></p>
        <ul>
            @foreach ($attachments as $attachment)
                <li>{{ $attachment->getClientOriginalName() }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
