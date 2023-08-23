<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="{{ $_ENV['description'] }}">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cookie">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    @if ($endpoint == 'article')
    <link rel="stylesheet" href="@asset('css/main.min.css')">
    @endif
    @if ($endpoint == 'intro')
    <link rel="stylesheet" href="@asset('css/styles.min.css')">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <script src="https://cdn.ckeditor.com/4.18.0/full-all/ckeditor.js"></script>
</head>

@if ($endpoint == 'intro')
    <body class="is-preload">
    <div id="wrapper">
@else
    <body>
@endif 