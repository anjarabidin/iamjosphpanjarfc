<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
{{-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet"> --}}

<!-- font awesome -->
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css"> --}}
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .text-lowercase {
        text-transform: lowercase !important;
    }

    body {
        background: rgba(0, 0, 0, 0.07);
        font-family: "Plus Jakarta Sans", sans-serif !important;
        color: #000;
    }

    .box {
        padding: 2rem;
        background: #fff;
    }

    .photo_profile {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 100%;
        object-fit: cover;
        margin-bottom: 0.75rem;
    }

    .head,
    .status {
        text-align: center;
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.125rem;
    }

    .sub {
        color: #333 !important;
        text-align: left;
        font-size: 0.75rem;
        line-height: 1.375rem;
        margin-bottom: 0.75rem;
    }

    .title {
        font-weight: 700;
        font-size: .875rem;
        color: #000;
    }

    .titles,
    .content {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .name_text {
        width: 7rem;
    }

    .content {
        color: #3d3d3d;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.00438rem;
    }

    .titles2 {
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #d9d9d9;
    }

    .date {
        color: #514eff;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.00438rem;
    }

    .d-flex {
        display: flex;
    }

    .w-100 {
        width: 100% !important;
    }

    .align-items-center {
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .justify-content-center {
        justify-content: center;
    }

    .arena_img {
        width: 4rem;
        height: 4rem;
        object-fit: cover;
    }

    .content_arena {
        margin-left: 1rem;
    }

    .content_arena h6 {
        font-weight: 600;
        font-size: 0.875rem;
        color: #000;
    }

    .content_arena p {
        color: #55565b;
        font-size: 0.75rem;
    }

    .book h6 {
        font-size: 0.75rem;
        font-weight: 700;
        color: #3d3d3d;
    }

    .book p,
    .price {
        font-size: 0.75rem;
        font-weight: 600;
        color: #3d3d3d;
    }

    .book_text {
        border-bottom: 2px solid #d9d9d9;
        width: 100%;
    }

    .book_text tr:nth-last-child(1) td:nth-child(2) {
        line-height: 6rem !important;
    }

    table tr {
        line-height: 1.75rem;
    }

    .total {
        font-size: .875rem;
        color: black;
        font-weight: 800;
    }

    .mb-0 {
        margin-bottom: 0 !important;
    }

    .mb-1 {
        margin-bottom: .25rem !important;
    }

    .mb-2 {
        margin-bottom: .5rem !important;
    }

    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .mb-4 {
        margin-bottom: 1.5rem !important;
    }

    .my-3 {
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
    }

    .mt-3 {
        margin-top: 1rem !important;
    }

    .mt-4 {
        margin-top: 1.5rem !important;
    }

    .logo {
        width: 5rem;
    }

    .img_icon {
        width: 1rem;
        margin-right: 1rem;
        height: 1.3;
    }

    .mx-auto {
        margin: 0 auto;
    }

    .assets {
        width: 10rem;
    }

    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: right;
    }

    .contentss p,
    .link a {
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
    }

    @media only screen and (max-width: 991.98px) {
        .box_content {
            width: 80% !important;
        }
    }

    @media only screen and (max-width: 575.98px) {
        .box_content {
            width: 100% !important;
        }
    }
</style>
