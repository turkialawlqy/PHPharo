<!DOCTYPE html>
<html lang="en">
<head>
	<title>Welcome to Pharaoh</title>

    <style>
        ::selection{
            background: #FA7701;
            color: white;
        }
        a{
            color: #003399;
        }
        body{
            color: #4F5155;
            font-weight: bold;
        }
        #content{
            margin: auto;
            text-align: center;
            box-shadow: 0 0 8px #ddd;
            border: 1px solid #ccc;
        }
        h1{
            color: #555;
        }
        #hdr{
            border-bottom: 1px solid #ccc;
        }
        #fotr{
            border-top: 1px solid #ccc;
            padding: 3px;
        }
        .highlight{
            padding: 3px; color: maroon
        }
        img{
            padding: 10px;
            box-shadow: 0 0 15px #ccc;
        }
    </style>

</head>
<body>
<div id="content">
    <div id="hdr"><h3>Wellcom to Pharaoh Framework Ver <?php echo PP_VERSION_NUMBER . ' - ' . PP_VERSION_NAME ?></h3></div>
    
    <p><img src="http://3.bp.blogspot.com/-R_4HpUg0s_Y/UsFURDXNwcI/AAAAAAAAAJQ/E_P8W71Bwe8/s320/Horus.jpg" alt="Horus" /></p>
    
    <p>This file can be found at <span class="highlight">'www/welcome.php'</span></p>
    
    <p>The main routes file is located at <span class="highlight">'www/index.php'</span></p>
    
    <p>See Wikis On <a target="_blank" href="https://github.com/alash3al/PHPharo/wiki">Github</a></p>
    
    <div id="fotr"><b> Powered By <a target="_blank" href="http://phpharo.blogspot.com">PHP Pharaoh Framework</a>, Brought to you by <a target="_blank" href="http://fb.com/alash3al">Mohammed Alashaal</a> </b> </div>
</div>
</body>
</html>