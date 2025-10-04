<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f4f4f7da;
            /* gray-200 */
        }

        .mobile-container {
            max-width: 420px;
            margin: 0 auto;
            background-color: #F9FAFB;
            /* gray-50 */
            min-height: 100vh;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .tab-active {
            border-bottom: 3px solid #3B82F6;
            /* blue-500 */
            color: #3B82F6;
            font-weight: 600;
        }

        .tab {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>

<body class="font-sans">

    @if ($microsite->is_active)
        <div class="max-w-md mx-auto my-0 ">

            <x-microsite.header :microsite="$microsite" />
            <div class="mx-4">
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eligendi iure ipsa laborum sapiente.
                    Totam, accusantium quae. Optio obcaecati placeat doloremque! Atque quae velit in laborum ipsam
                    voluptatibus officia. Quisquam, non. Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                    Eligendi iure ipsa laborum sapiente. Totam, accusantium quae. Optio obcaecati placeat doloremque!
                    Atque quae velit in laborum ipsam voluptatibus officia. Quisquam, non. Lorem ipsum, dolor sit amet
                    consectetur adipisicing elit. Eligendi iure ipsa laborum sapiente. Totam, accusantium quae. Optio
                    obcaecati placeat doloremque! Atque quae velit in laborum ipsam voluptatibus officia. Quisquam,
                    non. Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eligendi iure ipsa laborum sapiente.
                    Totam, accusantium quae. Optio obcaecati placeat doloremque! Atque quae velit in laborum ipsam
                    voluptatibus officia. Quisquam, non. Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                    Eligendi iure ipsa laborum sapiente. Totam, accusantium quae. Optio obcaecati placeat doloremque!
                    Atque quae velit in laborum ipsam voluptatibus officia. Quisquam, non. Lorem ipsum dolor sit amet
                    consectetur adipisicing elit. Harum officiis, laudantium quibusdam sequi a ducimus corrupti officia
                    vitae totam consequuntur rem veritatis voluptas facere sunt explicabo, nisi omnis nobis eos! Quasi,
                    modi tenetur? Quae, aperiam? Quidem cupiditate porro fuga culpa alias magnam recusandae nesciunt,
                    odit, blanditiis saepe velit minima! Dicta, asperiores. Nulla neque a maxime dolore repellat.
                    Dolor, hic magni. Debitis, natus fugit? Molestias suscipit animi praesentium obcaecati modi repellat
                    quisquam dignissimos, libero maiores officiis laudantium possimus facilis odio numquam nam, est
                    minima similique odit inventore earum, vel voluptatibus iure! Consequuntur odit dolorem cumque sed
                    aperiam provident ipsum modi soluta similique, ea odio doloremque numquam aut qui, velit impedit
                    voluptas error deleniti, sunt laudantium sequi accusantium incidunt. Quis, cumque asperiores.
                    Corporis repudiandae quisquam omnis. Ducimus fuga rem in atque, inventore quaerat quibusdam vel
                    dolorem perferendis libero consequuntur molestiae. Cumque, quis veritatis. Tenetur labore fugit fuga
                    neque quis molestias excepturi eaque?
                </p>
            </div>




        </div>
    @else
        <div class="mobile-container flex flex-col bg-red-100 text-red-800 p-4 text-center">
            <main class="flex-grow flex items-center justify-center">
                <div class="font-semibold">This site is currently inactive.</div>
            </main>
            <footer>
                {{ config('app.name') }}
            </footer>
        </div>
    @endif

</body>

</html>
