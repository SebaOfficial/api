<script>
  import { onMount } from "svelte";

  export let apiUrl;
  export let reprompt;

  const LOCALSTORAGE_ITEM = "newsletter.lastClosedAt";
  const NEVER_REPROMPT = -1;

  const SVGEmailData = `data:image/svg+xml;base64,${SVG_EMAIL_DATA}`;
  const SVGCloseData = `data:image/svg+xml;base64,${SVG_CLOSE_DATA}`;

  let display = false;
  let success = false;
  let email;
  let emailInput;
  let newsletterElement;
  let emailValid;

  onMount(() => {
    let lastClosedAt = localStorage.getItem(LOCALSTORAGE_ITEM);

    display = lastClosedAt
      ? lastClosedAt != NEVER_REPROMPT
        ? parseInt(lastClosedAt) + reprompt <= Date.now()
        : false
      : true;
  });

  function isValidEmail() {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateEmail() {
    const isValid = isValidEmail();
    emailInput.classList.toggle("green", isValid);
    emailInput.classList.toggle("red", !isValid);

    const button = document.getElementById("newsletter-btn");
  }

  function closeNewsletter(forever) {
    newsletterElement.classList.add("newsletter-inactive");
    setTimeout(() => {
      display = false;
    }, 600);
    localStorage.setItem(LOCALSTORAGE_ITEM, forever ? -1 : Date.now());
  }

  function subscribe(event) {
    event.preventDefault();

    if (!isValidEmail()) return;

    fetch(`${apiUrl}/newsletter/sub`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email,
      }),
    })
      .then((response) => {
        if (response.ok) return response.json();
        throw new Error("Network response was not ok.");
      })
      .then((data) => {
        if (!data.ok) throw new Error(data.error);

        success = true;
        display = false;
      })
      .catch((error) => {
        console.log("There was an error fetching the data: " + error);
      });
  }
</script>

{#if display}
  <div id="newsletter" bind:this={newsletterElement}>
    <h4>Subscribe</h4>
    <p>Subscribe to our newsletter & stay updated!</p>

    <button on:click={closeNewsletter} id="close" title="Close">
      <img src={SVGCloseData} alt="Close" />
    </button>

    <form>
      <div class="email-input">
        <img src={SVGEmailData} alt="Email" />

        <input
          type="email"
          placeholder="Enter your Email"
          required
          on:input={validateEmail}
          bind:this={emailInput}
          bind:value={email}
          class:green={emailValid}
          class:red={!emailValid && emailValid !== undefined}
        />
      </div>

      <button id="newsletter-btn" type="submit" on:click={subscribe} class:success>
        {success ? "Subscribed!" : "Subscribe"}
      </button>
    </form>
  </div>
{/if}

<style>
  #newsletter {
    position: fixed;
    width: 300px;
    bottom: 20px;
    right: 20px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: opacity 0.5s ease-out;
    background-color: rgba(0, 0, 0, 0.55);
    opacity: 0.9;
    color: white;
  }

  #close {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    background-color: transparent;
    border-color: transparent;
    cursor: pointer;
  }

  #close img {
    width: 100%;
    height: 100%;
  }

  h4 {
    margin-top: 0;
    margin-bottom: 10px;
    mix-blend-mode: difference;
  }

  p {
    margin: 0;
    mix-blend-mode: difference;
  }

  .email-input {
    margin-top: 10px;
    position: relative;
  }

  .email-input img {
    position: absolute;
    width: 18px;
    top: 25%;
    left: 13px;
  }

  input[type="email"] {
    mix-blend-mode: difference;
    padding: 10px 10px 10px 35px;
    width: 250px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    outline: none;
  }

  form button {
    margin-top: 20px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    mix-blend-mode: difference;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  form button:hover {
    background-color: #0056b3;
  }

  form button.success {
    background-color: rgba(40, 167, 69, 0.4);
  }
  form button.success:hover {
    background-color: rgba(40, 167, 69, 0.3);
  }

  .green {
    border: 1px solid rgba(40, 167, 69, 1) !important;
    background-color: rgba(40, 167, 69, 0.35) !important;
  }

  .red {
    border: 1px solid rgba(220, 53, 69, 1) !important;
    background-color: rgba(220, 53, 69, 0.35) !important;
  }

  @media (max-width: 500px) {
    #newsletter {
      display: none;
    }
  }
</style>
