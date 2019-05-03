<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sagrada Familia</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Custom styles for this template-->
    <link href="css/index.css" rel="stylesheet">
    <!-- Three JS -->
    <script src="js/three.min.js"></script>
    <script src = "js/OrbitControls.js"></script>
    <script src = "js/WebGL.js"></script>
    <script src = "js/inflate.min.js"></script>
    <script src="js/FBXLoader.js"></script>
  </head>

  <body>
    <div id="divContainer" style="height:100vh; width: 100vw"></div>
    <button id="camera-btn">Camera</button>
  </body>

  <script>
    let scene, camera, renderer, cube;
    var cameraFade = 0.05;
    var collisionObjects = [];
    let ADD = 0.1;
    var player = {};

    let createCameras = function () {
      const front = new THREE.Object3D();
      front.position.set(112, 100, 200);
      front.parent = player.object;

      const back = new THREE.Object3D();
      back.position.set(0, 100, -250);
      back.parent = player.object;

      const wide = new THREE.Object3D();
      wide.position.set(178, 139, 465);
      wide.parent = player.object;

      const overhead = new THREE.Object3D();
      overhead.position.set(0, 400, 0);
      overhead.parent = player.object;

      const collect = new THREE.Object3D();
      collect.position.set(40, 82, 94);
      collect.parent = player.object;

      player.cameras = {front, back, wide, overhead, collect};
      activeCamera = player.cameras.wide;
      cameraFade = 1;

      setTimeout(function () {
        activeCamera = player.cameras.back;
        cameraFade = 0.01;
        setTimeout(function () {
          cameraFade = 0.1;
        }, 1500);
      }, 2000)
    }

    let createCube = function () {
      var blackTexture = new THREE.TextureLoader().load("3D/textures/blackBackground.jpg");
      blackTexture.wrapS = THREE.RepeatWrapping;
      blackTexture.wrapT = THREE.RepeatWrapping;
      blackTexture.repeat.set(1, 1);
      let geometry = new THREE.BoxGeometry(10000, 5, 10000);
      let material = new THREE.MeshBasicMaterial({alphaMap: blackTexture});
      material.alphaMap = blackTexture;
      cube = new THREE.Mesh(geometry, material);
      cube.position.x = 0;
      cube.position.y = -10;
      cube.position.z = 0;
      collisionObjects.push(cube);
      scene.add(cube);
    };

    let createEnvironment = function () {
      let light = new THREE.DirectionalLight(0xffffff);
      light.position.set(0, 200, 100);
      light.castShadow = true;
      light.shadow.camera.top = 180;
      light.shadow.camera.bottom = -100;
      light.shadow.camera.left = -120;
      light.shadow.camera.right = 120;
      scene.add(light);

      // ground
      var mesh = new THREE.Mesh(new THREE.PlaneBufferGeometry(2000, 2000), new THREE.MeshPhongMaterial({color: 0x999999, depthWrite: false}));
      mesh.rotation.x = -Math.PI / 2;
      //mesh.position.y = -100;
      mesh.receiveShadow = true;
      scene.add(mesh);

      // Grid
      var grid = new THREE.GridHelper(2000, 40, 0x000000, 0x000000);
      grid.material.opacity = 0.2;
      grid.material.transparent = true;
      scene.add(grid);

      // Skysphere
      var textureSphere = new THREE.TextureLoader().load('https://upload.wikimedia.org/wikipedia/commons/2/26/Clouds_from_the_sky.jpg');
      textureSphere.wrapS = THREE.RepeatWrapping;
      textureSphere.wrapT = THREE.RepeatWrapping;
      textureSphere.repeat.set(1, 1);
      let materialSphere = new THREE.MeshBasicMaterial({map: textureSphere, side: THREE.DoubleSide});
      let geometrySphere = new THREE.SphereGeometry(700, 1000, 1000);
      var sphereSky = new THREE.Mesh(geometrySphere, materialSphere);

      scene.add(sphereSky);

    };

    let createScenario = function () {
      // Model of Streets
      const loader = new THREE.FBXLoader();
      loader.load('3D/scenario2.fbx', function (object) {

        object.traverse(function (child) {
          if (child.isMesh) {
            child.castShadow = true;
            child.receiveShadow = true;

            const oldMat = child.material;

            child.material = new THREE.MeshStandardMaterial({
              color: oldMat.color,
              map: oldMat.map,
              //side: THREE.DoubleSide,
              emissive: 0xffffff,
              emissiveIntensity: 0.1,
              metalness: 0,
              roughness: 0.5
            });

          }
        });
        scene.add(object);
      });
    }

    let sagradaFamilia = function () {
      // Model
      const loader = new THREE.FBXLoader();
      loader.load('3D/sagrada familia3.fbx', function (object) {

        object.traverse(function (child) {
          if (child.isMesh) {
            child.castShadow = true;
            child.receiveShadow = true;

            const oldMat = child.material;

            child.material = new THREE.MeshStandardMaterial({
              color: oldMat.color,
              map: oldMat.map,
              //side: THREE.DoubleSide,
              emissive: 0xffffff,
              emissiveIntensity: 0.1,
              metalness: 0,
              roughness: 0.5
            });

          }
        });
        scene.add(object);
      });
    };

    let dotravelLogo = function () {

      const loader = new THREE.FBXLoader();
      loader.load('3D/dotravellogo2.fbx', function (object) {

        object.mixer = new THREE.AnimationMixer(object);
        object.castShadow = true;
        object.name = "logo";
        object.traverse(function (child) {
          if (child.isMesh) {
            child.castShadow = false;
            child.receiveShadow = false;
          }
        });
        scene.add(object);

      });
    };

    let createLights = function () {
      let light = new THREE.HemisphereLight(0xECFFFF, 0xFFF7DF, 0.3);
      light.position.set(0, 20000, 0);
      scene.add(light);

      //create the light
      //let light = new THREE.HemisphereLight(0xffffff, 0x444444);
      //light.position.set(0, 200, 0);
      //scene.add(light);

      light = new THREE.DirectionalLight(0xFFF7DF);
      light.position.set(0, 200, 100);
      light.castShadow = true;
      light.intensity = .1;
      light.shadow.camera.top = 180;
      light.shadow.camera.bottom = -100;
      light.shadow.camera.left = -120;
      light.shadow.camera.right = 120;
      light.shadow.mapSize.width = 4096;
      light.shadow.mapSize.height = 2048;
      scene.add(light);

      light = new THREE.DirectionalLight(0xFFF7DF, 0.2);
      light.position.set(0, 200, -100);
      light.castShadow = false;
      light.shadow.camera.top = 180;
      light.shadow.camera.bottom = -100;
      light.shadow.camera.left = -120;
      light.shadow.camera.right = 120;
      scene.add(light);

      light = new THREE.PointLight(0xffffff, 1.1, 80);
      light.position.set(50, 60, -50);
      scene.add(light);
      
      light = new THREE.PointLight(0xffffff, 1.1, 80);
      light.position.set(100, 60, -50);
      scene.add(light);
    }

    let createCamera = function () {
      // create an locate the camera
      camera = new THREE.PerspectiveCamera(75, $('#divContainer').innerWidth() / $('#divContainer').innerHeight(), 1, 1000);
      camera.position.z = 5000;
      camera.position.y = 100;
      camera.position.z = 100;
      camera.rotation.x = 100;
      camera.lookAt(0, 0, 1);
      //let axes = new THREE.AxesHelper(5);
      //scene.add(axes);
    }

    let createRender = function () {
      renderer = new THREE.WebGLRenderer();
      renderer.setSize($('#divContainer').innerWidth(), $('#divContainer').innerHeight());
      renderer.shadowMap.enabled = true;
      renderer.shadowMap.type = THREE.PCFShadowMap;
      $('#divContainer').append(renderer.domElement);
    }

    let createControls = function () {
      var controls = new THREE.OrbitControls(camera, renderer.domElement);
      controls.minPolarAngle = 0; // radians
      controls.maxPolarAngle = 1.25 // Math.PI / 2.5; // radians
      controls.minAzimuthAngle = -Infinity; // radians
      controls.maxAzimuthAngle = Infinity; // radians
      controls.update();
    }

    let createScene = function () {
      scene = new THREE.Scene();
      scene.background = new THREE.Color(0xa0a0a0);
      scene.fog = new THREE.Fog(0xa0a0a0, 200, 1000);
    }

    let init = function () {

      createScene();
      createCameras();
      createCamera();
      createLights();
      createEnvironment();
      createScenario();
      sagradaFamilia();
      dotravelLogo();
      createRender();
      createControls();
      setActiveCamera(player.cameras.back);

    };

    let mainLoop = function () {
      renderer.render(scene, camera);
      requestAnimationFrame(mainLoop);

      //console.log(player.cameras.active);
      /*camera.position.lerp(player.cameras.active.getWorldPosition(new THREE.Vector3()), cameraFade);
       let pos;
       pos = player.object.position.clone();
       pos.y += 60;
       console.log(pos);
       camera.lookAt(pos);*/

    };

    let switchCamera = function (fade = 0.05) {
      const cams = Object.keys(player.cameras);
      cams.splice(cams.indexOf('active'), 1);
      let index;
      for (let prop in player.cameras) {
        if (player.cameras[prop] == player.cameras.active) {
          index = cams.indexOf(prop) + 1;
          if (index >= cams.length)
            index = 0;
          player.cameras.active = player.cameras[cams[index]];
          break;
        }
      }
      cameraFade = fade;
    }

    let setActiveCamera = function (object) {
      player.cameras.active = object;
    }

    init();
    mainLoop();

    window.addEventListener('resize', () => {
      var controls = new THREE.OrbitControls(camera);
      controls.update();
      renderer.setSize($('#divContainer').innerWidth(), $('#divContainer').innerHeight());
      camera.aspect = $('#divContainer').innerWidth() / $('#divContainer').innerHeight()
      camera.updateProjectionMatrix();
    });

    document.getElementById("camera-btn").onclick = function () {
      //switchCamera();
    };


  </script>
</html>



