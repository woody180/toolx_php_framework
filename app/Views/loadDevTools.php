<?php 
    if (ENV !== 'development') return;

    $devTools = file_get_contents(dirname(APPROOT) . '/log.txt', true); 
    $devTools = json_decode($devTools);
    $allRoutes = $devTools->all_routes;

    unset($devTools->all_routes);
?>

<div id="dev-tools-root">
    <span id="close-dev-tools"><span>×</span></span>
    <div id="toggle-dev-tools">Dev Tools</div>

    <ul>
        
        <?php foreach ($devTools as $key => $value): ?>
        <li>
            <b><?= $key ?></b>
            <code><?= $value ?></code>
        </li>
        <?php endforeach; ?>

    </ul>

    <ul>
        <li><b>GET Request Routes</b></li>
       <?php foreach ($allRoutes as $key => $route): ?>
        <li>
            <b><?= $key ?></b>
            <code><a href="<?= baseUrl($route) ?>"><?= $route ?></a></code>
        </li>
        <?php endforeach; ?>

    </ul>

</div>



<script>
    document.head.insertAdjacentHTML('beforeend', `<style type="text/css">
        #dev-tools-root {
            position: fixed;
            z-index: 9999;
            bottom: 0;
            left: 0;
            background-color: #e3f1ee;
            width: 100%;
            display: flex;
            border-top: 5px solid #62686721;
        }

        #dev-tools-root ul {
            list-style: none;
            width: 96%;
            height: 21vh;
            margin: 0;
            padding: 10px 20px;
            overflow-y: auto;
            flex: 1;
            display: none;
        }

        #dev-tools-root.active ul {
            list-style: none;
            width: 96%;
            height: 21vh;
            margin: 0;
            padding: 10px 20px;
            overflow-y: auto;
            flex: 1;
            display: block;
        }


        #dev-tools-root #toggle-dev-tools {
            display: flex;
            position: absolute;
            top: -40px;
            right: 0;
            width: 100px;
            height: 35px;
            background: #e3f1ee;
            z-index: 9999;
            justify-content: center;
            align-items: center;
            border-top-left-radius: 5px;
            color: black;
            cursor: pointer;
        }

        #dev-tools-root.active #toggle-dev-tools{
            display: none;
        }

        #dev-tools-root li {
            margin: 0;
            border-bottom: 1px solid #c8dbd7;
            padding: 10px 0;
        }

        #dev-tools-root li hr {
            appearance: none;
            border: none;
            border-bottom: 1px solid #c8dbd7;
            padding: 3px 0 0 0;
        }

        #dev-tools-root li:last-child {
            border-bottom: none;
        }

        #dev-tools-root #close-dev-tools {
            position: absolute;
            right: 25px;
            top: 10px;
            background-color: #00000091;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            margin: 0;
            font-size: 24px;
            cursor: pointer;
        }

        #dev-tools-root #close-dev-tools span {
            position: relative;
            top: -3px;
        }
    </style>`);




    document.querySelector('#toggle-dev-tools').onclick = () => {
        document.querySelector('#dev-tools-root').classList.add('active');
        localStorage.setItem('dev_tools', '1');
    };
    document.querySelector('#close-dev-tools').onclick = () => {
        document.querySelector('#dev-tools-root').classList.remove('active');
        localStorage.removeItem('dev_tools');
    };


    if (localStorage.getItem('dev_tools') == '1') {
        document.querySelector('#dev-tools-root').classList.add('active');
    }

</script>