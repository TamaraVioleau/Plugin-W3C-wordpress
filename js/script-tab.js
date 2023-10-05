document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll('[role="tab"]');
  let firstTab = tabs[0];
  let lastTab = tabs[tabs.length - 1];

  tabs.forEach((tab, index) => {
    tab.addEventListener("keydown", function (event) {
      let flag = false;

      switch (event.key) {
        case "ArrowLeft":
          moveFocusToPreviousTab(tab);
          flag = true;
          break;

        case "ArrowRight":
          moveFocusToNextTab(tab);
          flag = true;
          break;

        default:
          break;
      }

      if (flag) {
        event.preventDefault();
        event.stopPropagation();
      }
    });
  });

  function moveFocusToTab(currentTab) {
    currentTab.focus();
  }

  function moveFocusToPreviousTab(currentTab) {
    const index = Array.from(tabs).indexOf(currentTab);

    if (currentTab === firstTab) {
      moveFocusToTab(lastTab);
    } else {
      moveFocusToTab(tabs[index - 1]);
    }
  }

  function moveFocusToNextTab(currentTab) {
    const index = Array.from(tabs).indexOf(currentTab);

    if (currentTab === lastTab) {
      moveFocusToTab(firstTab);
    } else {
      moveFocusToTab(tabs[index + 1]);
    }
  }
});
