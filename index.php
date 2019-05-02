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
  <link href="css/framework.css" rel="stylesheet">
		<!-- Custom fonts for this template-->
  <link href="css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		<!-- Three JS -->
		<script src="js/three.min.js"></script>
		<script src = "js/OrbitControls.js"></script>
		<script src = "js/WebGL.js"></script>
		<script src = "js/inflate.min.js"></script>
		<script src="js/FBXLoader.js"></script>
	</head>

	<body>
		<div id="divContainer" style="height:100vh; width: 100vw">
		</div>
	</body>

	<script>
		let scene, camera, renderer, cube;
		let ADD = 0.1;

		let createCube = function () {
			let geometry = new THREE.BoxGeometry(10, 10, 10);
			let material = new THREE.MeshBasicMaterial({color: 0x00a1cb});
			cube = new THREE.Mesh(geometry, material);
			cube.position.y = 10;
			scene.add(cube);
		};

		let createScenario = function () {
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

		let init = function () {
			// create the scene
			scene = new THREE.Scene();
			scene.background = new THREE.Color(0xa0a0a0);
			scene.fog = new THREE.Fog(0xa0a0a0, 200, 1000);

			//create the light
			//let light = new THREE.HemisphereLight(0xffffff, 0x444444);
			//light.position.set(0, 200, 0);
			//scene.add(light);

			// create an locate the camera
			camera = new THREE.PerspectiveCamera(75, $('#divContainer').innerWidth() / $('#divContainer').innerHeight(), 1, 1000);
			camera.position.z = 5000;
			camera.position.y = 100;
			camera.position.z = 100;
			camera.rotation.x = 100;
			camera.lookAt(0, 0, 1);
			//let axes = new THREE.AxesHelper(5);
			//scene.add(axes);


			let light = new THREE.HemisphereLight(0xECFFFF, 0xFFF7DF, 0.3);
			light.position.set(0, 20000, 0);
			scene.add(light);

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

			var controls = new THREE.OrbitControls(camera);
			controls.update();
			//createCube();
			createScenario();
			sagradaFamilia();
			// create the renderer   
			renderer = new THREE.WebGLRenderer();
			renderer.setSize($('#divContainer').innerWidth(), $('#divContainer').innerHeight());
			renderer.shadowMap.enabled = true;
			renderer.shadowMap.type = THREE.PCFShadowMap;

			$('#divContainer').append(renderer.domElement);
		};

		let mainLoop = function () {
			//cube.rotation.y += ADD;

			renderer.render(scene, camera);
			requestAnimationFrame(mainLoop);
		};

		init();
		mainLoop();

		window.addEventListener('resize', () => {
			var controls = new THREE.OrbitControls(camera);
			controls.update();
			renderer.setSize($('#divContainer').innerWidth(), $('#divContainer').innerHeight());
			camera.aspect = $('#divContainer').innerWidth() / $('#divContainer').innerHeight()
			camera.updateProjectionMatrix();
		});
	</script>
</html>



