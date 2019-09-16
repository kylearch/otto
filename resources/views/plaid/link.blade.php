<!doctype html>
<html>
    <head>

    </head>
    <body>
        <form id='plaid-link-form'></form>
        <script
                src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"
                data-client-name="otto"
                data-form-id="plaid-link-form"
                data-key="{{ env('PLAID_PUBLIC_KEY') }}"
                data-product="transactions"
                data-env="development">
        </script>
    </body>
</html>



