const $deleteModal = document.querySelector("#delete-modal");
const $editModal = document.querySelector("#edit-modal");
const $overlay = document.querySelector("#overlay");

const showEditModal = () => {
  console.log("Hola");

  $overlay.classList.add("active");
  $editModal.style.animation = "modalIn .8s forwards";
};

//Escondiendo el modal
const hideEditModal = () => {
  $overlay.classList.remove("active");
  $editModal.style.animation = "modalOut .8s forwards";
};

const showDeleteModal = () => {
  const pokemonId = document.querySelector('.Pokemon__id').textContent;
  document.getElementById('pokemonIdToDelete').value = pokemonId;
  console.log(pokemonId);
  $overlay.classList.add("active");
  $deleteModal.style.animation = "modalIn .8s forwards";
};

//Escondiendo el modal
const hideDeleteModal = () => {
  $overlay.classList.remove("active");
  $deleteModal.style.animation = "modalOut .8s forwards";
};

function logout() {
  sessionStorage.clear();
  // Eliminar la sesión y redirigir a la página de inicio de sesión
  fetch("/php/logout.php")
    .then(() => {
      window.location.assign("signin.php");
    })
    .catch((error) => {
      console.error("Error during logout:", error);
    });
}

const verifyProfile = () => {
  // Verificar si no hay una sesión iniciada y redirigir a la página de inicio de sesión
  fetch("/php/session.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.isLoggedIn) {
        window.location.assign("signin.php");
      }
    })
    .catch((error) => {
      console.error("Error during session verification:", error);
    });
};

function displayPokemon(i) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "/php/getPokemon.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function() {
    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
      let pokemon = JSON.parse(this.responseText);
      updatePokemonDisplay(pokemon);
    }
  }

  xhr.send(`pokemonId=${i}`);
}

function updatePokemonDisplay(pokemon) {
  // Aqui actualizas el DOM con los nuevos datos del Pokemon
  // Por ejemplo:
  document.querySelector('.Pokemon__img').src = `https://img.pokemondb.net/artwork/large/${pokemon.nombre}.jpg`;
  document.querySelector('.Pokemon__img').alt = pokemon.nombre;
  document.querySelector('.Pokemon__name').textContent = pokemon.nombre;
  document.querySelector('.Pokemon__id').textContent = pokemon.id;
  document.querySelector('.Pokemon__height').textContent = pokemon.altura + " fts.";
  document.querySelector('.Pokemon__baxp').textContent = "BAXP: " + pokemon.baxp + " pts.";
  
  
  // Y así con el resto de los datos del Pokemon
}
