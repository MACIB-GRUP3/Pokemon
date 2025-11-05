(async function load() {
  const API_URL = "https://pokeapi.co/api/v2/pokemon?limit=151";

  //Fetch al api
  async function getData(url) {
    const response = await fetch(url);
    const data = await response.json();
    if (data.results.length > 0) {
      return data.results;
    } else {
      throw new Error("No se encontró ningún resultado.");
    }
  }

  //Nuevo fetch dentro de la data para encontrar los atributos del pokémon
  //De paso se ejecuta el renderPokemon por cada pokemon cargado
  async function loadPokemonData(pokemons) {
    pokemons.forEach(async (pokemon) => {
      const response = await fetch(pokemon.url);
      const data = await response.json();
      renderPokemon(data, $pokeRow);
    });
  }

  //Pokémon card
  const pokemonItemTemplate = (pokemon) => {
    return `
        <div class="PokeCard" data-id="${pokemon.id}" data-name="${pokemon.name}">
        <img class="PokeCard__img" src="https://img.pokemondb.net/artwork/large/${pokemon.name}.jpg" alt="Pokémon" style="width:100%">
        <div class="PokeCard__container">
        <h3>${pokemon.name}</h3>
        </div>
        </div>
        `;
  };

  //Creando el html element en base de la pokeCard o HTMLString
  const createTemplate = (HTMLString) => {
    const html = document.implementation.createHTMLDocument();
    html.body.innerHTML = HTMLString;
    return html.body.children[0];
  };

  //Handler del modal (ventana al hacer click)
  const $modal = document.querySelector("#modal");
  const $overlay = document.querySelector("#overlay");
  const $hideModal = document.querySelector("#hide-modal");
  const $addPokemonButton = document.querySelector("#pokemon-add");

  const $modalPokeName = $modal.querySelector("h1");
  const $modalImage = $modal.querySelector("img");
  const $modalPokeWeight = $modal.querySelector("#pokemon-weight");
  const $modalPokeHeight = $modal.querySelector("#pokemon-height");
  const $modalPokeExperience = $modal.querySelector("#pokemon-experience");

  //Encontrando pokémon por id
  const findPokemonByID = (id) => {
    return pokemons.find((pokemon) => pokemon.id === parseInt(id, 10));
  };

  const showModal = ($element) => {
    $overlay.classList.add("active");
    $modal.style.animation = "modalIn .8s forwards";
    const id = $element.dataset.id;
    const pokemon = findPokemonByID(id);
    $modalPokeName.textContent = pokemon.name;
    $modalPokeExperience.textContent = `BAXP: ${pokemon.base_experience} pts.`;
    $modalPokeWeight.textContent = `Weight: ${pokemon.weight} lbs.`;
    $modalPokeHeight.textContent = `Height: ${pokemon.height} fts.`;
    $modalImage.setAttribute(
      "src",
      `https://img.pokemondb.net/artwork/large/${pokemon.name}.jpg`
    );
    $modalImage.id = `${id}`;
    $modalPokeExperience.baxp = `${pokemon.base_experience}`;
    $modalPokeWeight.weight = `${pokemon.weight}`;
    $modalPokeHeight.height = `${pokemon.height}`;
  };

  //Escondiendo el modal
  const hideModal = () => {
    $overlay.classList.remove("active");
    $modal.style.animation = "modalOut .8s forwards";
  };

  //Añadir pokémon a pokédex (bda)
  const addPokemon = () => {
    //creamos los cookies de los specs del pokemon seleccionado
    createCookie("img_id", $modalImage.id, "1");
    createCookie("especie", $modalPokeName.textContent, "1");
    createCookie("peso", $modalPokeWeight.weight, "1");
    createCookie("altura", $modalPokeHeight.height, "1");
    createCookie("baxp", $modalPokeExperience.baxp, "1");
    document.getElemntById("pokemon-add").submit();
    hideModal();
  };

  function createCookie(name, value, days) {
    var expires;
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toGMTString();
    } else {
      expires = "";
    }
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
  }

  $hideModal.addEventListener("click", hideModal);
  $addPokemonButton.addEventListener("click", addPokemon);

  const addEventClick = ($element) => {
    $element.addEventListener("click", () => {
      showModal($element);
    });
  };

  //Rendereando el pokémon card
  const renderPokemon = (pokemon, $container) => {
    pokemons.push(pokemon);
    const HTMLString = pokemonItemTemplate(pokemon);
    const pokeCard = createTemplate(HTMLString);
    $container.append(pokeCard);
    addEventClick(pokeCard);
  };

  let pokemons = []; //Arreglo de promesas pokémon, aquí se almacenan todas mis esperanzas e ilusiones
  let $pokeRow = document.querySelector("#pokeRow");
  const data = await getData(API_URL);
  loadPokemonData(data);
  $pokeRow.children[0].remove();
})();

//Filtrando pokémon
const filterPokemon = () => {
  $filter = document.querySelector("#filter-pokemon");
  wantedPokemon = $filter.value.toUpperCase();
  $pokeRow = document.querySelector("#pokeRow");
  $pokeCards = $pokeRow.querySelectorAll(".PokeCard");
  for (i = 0; i < $pokeCards.length; i++) {
    $pokeCardsContainer = $pokeCards[i].querySelector(".PokeCard__container");
    name = $pokeCardsContainer.innerText;
    if (name.toUpperCase().indexOf(wantedPokemon) > -1) {
      $pokeCards[i].style.display = "";
    } else {
      $pokeCards[i].style.display = "none";
    }
  }
};

form = document.querySelector("#main-form");
submit = document.querySelector("#pokemon-add");
form.addEventListener("submit", () => {
  history.pushState({}, "", "");
});


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
