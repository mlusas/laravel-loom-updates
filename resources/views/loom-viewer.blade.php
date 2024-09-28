<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loom Videos in the Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: stretch;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            max-width: 1080px;
            width: 100%;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .thumbnail {
            width: 120px;
            height: 67.5px;
            background-size: cover;
            background-position: center;
            cursor: pointer;
        }
        .title {
            max-width: 320px;
            color: #0066cc;
            text-decoration: none;
            cursor: pointer;
        }
        .title:hover {
            text-decoration: underline;
        }
        .title-cell {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }
        .tag {
            background-color: #f2f2f2;
            color: #5c5c5c;
            padding: 4px 6px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Loom Videos in the Code</h1>
        <table>
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>File Path</th>
                    <th>Date</th>
                    <th>Author</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loomUrls as $url)
                    <tr>
                        <td>
                            <div class="thumbnail" style="background-image: url('{{ $url->image_url }}')" onclick="window.open('{{ $url->url }}', '_blank')"></div>
                        </td>
                        <td>
                            <div class="title-cell">
                                <div><a href="{{ $url->url }}" target="_blank" class="title">{{ $url->title }}</a></div>
                                <div>
                                    <span class="tag">{{ $url->tag }}</span>
                                </div>
                            </div>            
                        </td>
                        <td>{{ $url->file_path }}:{{ $url->line_number }}</td>
                        <td>{{ $url->date }}</td>
                        <td>{{ $url->author }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>