<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>
    <script>
    $(document).ready(function(){
        $("buttton").click(function(){
            $("#container").load("Demo_test.txt");
        });
    });
    </script>

    </head>
    <body>
        <div id="container">
            <h2>Let jQuery AJAX Change This Text</h2>
        </div>
        <br>
        <buttton>Get file Content</buttton>
    </body>


// src="jquery-1.11.1.js"

