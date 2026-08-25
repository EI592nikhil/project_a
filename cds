import { performSearch, saveSearchTerm } from '../searchboxSuggestions/_searchboxSuggestions';

export default class HeaderNavigation {
  private root: HTMLElement;

  constructor(root: HTMLElement) {
    this.root = root;
    this.init();
  }

  init() {
    const toggle = this.root.querySelector('.header-nav__toggle') as HTMLElement | null;
    const header = this.root.querySelector('.header-nav') as HTMLElement | null;

    const searchTrigger = this.root.querySelector(
      '.header-nav__search-mobile',
    ) as HTMLElement | null;

    if (!toggle || !header) return;

    const toggleEls = this.root.querySelectorAll(
      '.cmp-onelinknav__toggle, .cmp-onelinknav-v2__toggle',
    );

    // ------------------------------------------------------------
    // Current region
    // ------------------------------------------------------------
    toggleEls.forEach((toggleEl) => {
      const instance = (toggleEl as HTMLElement).closest(
        '.cmp-onelinknav, .cmp-onelinknav-v2',
      ) as HTMLElement | null;

      if (!instance) return;

      const urlMatchedItem = Array.from(
        instance.querySelectorAll(
          '.cmp-onelinknav__list-item, .cmp-onelinknav-v2__list-item',
        ),
      ).find((item) => {
        const path = item
          .querySelector(
            '.cmp-onelinknav__lang-link, .cmp-onelinknav-v2__lang-link',
          )
          ?.getAttribute('data-onelink-path');

        return !!path && window.location.href.indexOf(path) > -1;
      }) as HTMLElement | undefined;

      const activeItem = instance.querySelector(
        '.cmp-onelinknav__list-item.active, .cmp-onelinknav-v2__list-item.active',
      ) as HTMLElement | null;

      const fallbackItem = instance.querySelector(
        '.cmp-onelinknav__list-item, .cmp-onelinknav-v2__list-item',
      ) as HTMLElement | null;

      const selectedItem = urlMatchedItem ?? activeItem ?? fallbackItem;

      if (selectedItem) {
        selectedItem.classList.add('active');

        const regionText = selectedItem
          .querySelector(
            '.cmp-onelinknav__lang-link, .cmp-onelinknav-v2__lang-link',
          )
          ?.textContent?.trim();

        const flagSrc = selectedItem.querySelector('img')?.getAttribute('src');

        if (regionText) {
          this.injectRegionLabel(toggleEl as HTMLElement, regionText);
        }

        if (flagSrc) {
          this.injectRegionFlag(toggleEl as HTMLElement, flagSrc);
        }
      }
    });

    this.syncAccountBlockPosition();
    this.syncChatUIPosition();

    this.bindOnelinkDropdownToggle();
    this.bindSignInDropdown();

    this.bindSearchTrigger(searchTrigger, header, toggle);
    this.bindSearchIconTrigger();

    this.setHeaderHeightVar();
    this.bindStickyHeader();

    // ------------------------------------------------------------
    // Main navigation active state
    // ------------------------------------------------------------
    this.root.querySelectorAll('.main-nav ul li').forEach((item) => {
      const link = item.querySelector('a') as HTMLAnchorElement | null;

      if (link && link.pathname === window.location.pathname) {
        item.classList.add('active');
      }
    });

    // ------------------------------------------------------------
    // OneLink items
    // ------------------------------------------------------------
    const onelinkItems = this.root.querySelectorAll(
      '.cmp-onelinknav__list-item, .cmp-onelinknav-v2__list-item',
    );

    onelinkItems.forEach((item) => {
      item.addEventListener('click', () => {
        const instance = (item as HTMLElement).closest(
          '.cmp-onelinknav, .cmp-onelinknav-v2',
        ) as HTMLElement | null;

        const toggleEl = instance?.querySelector(
          '.cmp-onelinknav__toggle, .cmp-onelinknav-v2__toggle',
        ) as HTMLElement | null;

        if (!toggleEl) return;

        instance
          ?.querySelectorAll(
            '.cmp-onelinknav__list-item.active, .cmp-onelinknav-v2__list-item.active',
          )
          .forEach((el) => el.classList.remove('active'));

        item.classList.add('active');

        const langLink = item.querySelector(
          '.cmp-onelinknav__lang-link, .cmp-onelinknav-v2__lang-link',
        );

        const regionText = langLink?.textContent?.trim();
        const flagSrc = item.querySelector('img')?.getAttribute('src');

        if (regionText) {
          this.injectRegionLabel(toggleEl, regionText);
        }

        if (flagSrc) {
          this.injectRegionFlag(toggleEl, flagSrc);
        }

        const localePath = langLink?.getAttribute('data-onelink-path');

        if (localePath) {
          this.navigateToLocale(localePath);
        }
      });
    });

    // ------------------------------------------------------------
    // Mobile menu toggle
    // ------------------------------------------------------------
    const activate = (e: Event) => {
      e.stopPropagation();

      const isOpen = header.classList.toggle('is-open');

      this.root.classList.toggle('is-open', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));

      if (!isOpen) {
        this.closeAllOnelinkPopovers(header);
      }
    };

    toggle.addEventListener('click', activate);

    toggle.addEventListener('keydown', (e: KeyboardEvent) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        activate(e);
      }
    });

    // ------------------------------------------------------------
    // Click outside
    // ------------------------------------------------------------
    document.addEventListener('click', (e) => {
      const target = e.target as Node;

      const insideHeader = header.contains(target);

      const dropdownElements = Array.from(
        this.root.querySelectorAll(
          '.cmp-onelinknav__toggle, ' +
            '.cmp-onelinknav-v2__toggle, ' +
            '.cmp-onelinknav__popover, ' +
            '.cmp-onelinknav-v2__popover, ' +
            '.headerUserDropDown',
        ),
      );

      const insideDropdown = dropdownElements.some((el) =>
        el.contains(target),
      );

      if (!insideHeader && !insideDropdown && !toggle.contains(target)) {
        header.classList.remove('is-open');
        this.root.classList.remove('is-open');

        toggle.setAttribute('aria-expanded', 'false');

        this.closeAllOnelinkPopovers(this.root);
        this.closeSignInDropdown();
      }
    });

    // ------------------------------------------------------------
    // ESC
    // ------------------------------------------------------------
    document.addEventListener('keydown', (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        header.classList.remove('is-open');
        this.root.classList.remove('is-open');

        toggle.setAttribute('aria-expanded', 'false');

        this.closeAllOnelinkPopovers(this.root);
        this.closeSignInDropdown();
      }
    });

    // ------------------------------------------------------------
    // Resize
    // ------------------------------------------------------------
    let lastIsMobile = window.matchMedia('(max-width: 1199px)').matches;

    window.addEventListener('resize', () => {
      const isMobile = window.matchMedia('(max-width: 1199px)').matches;

      if (lastIsMobile && !isMobile) {
        header.classList.remove('is-open');
        this.root.classList.remove('is-open');

        toggle.setAttribute('aria-expanded', 'false');

        this.closeSignInDropdown();
      }

      lastIsMobile = isMobile;

      this.syncAccountBlockPosition();
      this.syncChatUIPosition();
    });
  }

  // ------------------------------------------------------------
  // SIGN-IN / USER DROPDOWN
  //
  // Important:
  // - Only handles logged-in user button.
  // - Does NOT touch .signInButton.
  // - Uses delegated click so it still works if singlesignin-v2
  //   replaces/re-renders its DOM after login.
  // ------------------------------------------------------------
  private bindSignInDropdown() {
    this.root.addEventListener('click', (event) => {
      const target = event.target as HTMLElement;

      const userButton = target.closest(
        '.headerUserName.welcome-message-button, .headerUserName',
      ) as HTMLElement | null;

      if (!userButton) return;

      // Do not interfere with the logged-out Sign In button.
      if (target.closest('.signInButton')) {
        return;
      }

      const signInContainer = userButton.closest(
        '.singlesignin, .headerUserDropDown',
      ) as HTMLElement | null;

      if (!signInContainer) return;

      const dropdown = signInContainer.querySelector(
        '#card-div.userDropDown, .userDropDown',
      ) as HTMLElement | null;

      if (!dropdown) return;

      event.preventDefault();
      event.stopPropagation();

      const isHidden = dropdown.classList.contains('hide');

      if (isHidden) {
        // Close OneLink/country dropdown when user dropdown opens.
        this.closeAllOnelinkPopovers(this.root);

        dropdown.classList.remove('hide');
        userButton.classList.add('active');
        userButton.setAttribute('aria-expanded', 'true');
      } else {
        dropdown.classList.add('hide');
        userButton.classList.remove('active');
        userButton.setAttribute('aria-expanded', 'false');
      }
    });
  }

  private closeSignInDropdown() {
    this.root
      .querySelectorAll(
        '.singlesignin #card-div.userDropDown, ' +
          '.singlesignin .userDropDown',
      )
      .forEach((dropdown) => {
        dropdown.classList.add('hide');
      });

    this.root
      .querySelectorAll(
        '.singlesignin .headerUserName.welcome-message-button, ' +
          '.singlesignin .headerUserName',
      )
      .forEach((button) => {
        button.classList.remove('active');
        button.setAttribute('aria-expanded', 'false');
      });
  }

  // ------------------------------------------------------------
  // Header height
  // ------------------------------------------------------------
  private setHeaderHeightVar() {
    const wrapper = this.root as HTMLElement;

    const header = wrapper.querySelector(
      '.header-nav',
    ) as HTMLElement | null;

    const update = () => {
      const height = header
        ? header.getBoundingClientRect().height
        : wrapper.offsetHeight;

      wrapper.style.setProperty(
        '--header-nav-height',
        `${height}px`,
      );
    };

    update();

    window.addEventListener('resize', update);

    this.root.addEventListener('transitionend', update);
  }

  // ------------------------------------------------------------
  // Sticky header
  // ------------------------------------------------------------
  private bindStickyHeader() {
    const wrapper = this.root as HTMLElement;

    const header = this.root.querySelector(
      '.header-nav',
    ) as HTMLElement | null;

    if (!header) return;

    const STICKY_THRESHOLD = 10;

    let prevScrollY = window.pageYOffset;

    const onScroll = () => {
      const currentScrollY = window.pageYOffset;

      if (currentScrollY > STICKY_THRESHOLD) {
        header.classList.add('has-sticky');
        wrapper.classList.add('has-sticky-spacer');

        if (
          header.classList.contains('is-open') ||
          header.classList.contains('search')
        ) {
          header.classList.remove('slide-up');
        } else if (prevScrollY < currentScrollY) {
          header.classList.add('slide-up');
        } else if (prevScrollY > currentScrollY) {
          header.classList.remove('slide-up');
        }
      } else {
        header.classList.remove('has-sticky', 'slide-up');
        wrapper.classList.remove('has-sticky-spacer');
      }

      prevScrollY = currentScrollY;
    };

    window.addEventListener(
      'scroll',
      this.throttle(onScroll, 100),
      { passive: true },
    );

    onScroll();
  }

  // ------------------------------------------------------------
  // Throttle
  // ------------------------------------------------------------
  private throttle(func: () => void, delay: number) {
    let inProgress = false;

    return () => {
      if (inProgress) return;

      inProgress = true;

      setTimeout(() => {
        func();
        inProgress = false;
      }, delay);
    };
  }

  // ------------------------------------------------------------
  // Mobile search / chat
  // ------------------------------------------------------------
  private bindSearchTrigger(
    searchTrigger: HTMLElement | null,
    header: HTMLElement,
    toggle: HTMLElement,
  ) {
    if (!searchTrigger) return;

    searchTrigger.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      const w = window as Window &
        typeof globalThis & {
          ChatUI?: {
            open?: () => void;
          };
        };

      const container = this.root.querySelector('#chat-ui');

      if (w.ChatUI?.open && container) {
        w.ChatUI.open();
        return;
      }

      const isOpeningSearch = !header.classList.contains('search');

      if (isOpeningSearch) {
        header.classList.remove('is-open');
        this.root.classList.remove('is-open');

        toggle.setAttribute('aria-expanded', 'false');

        this.closeAllOnelinkPopovers(this.root);
        this.closeSignInDropdown();
      }

      header.classList.toggle('search');
    });
  }

  // ------------------------------------------------------------
  // Desktop search
  // ------------------------------------------------------------
  private bindSearchIconTrigger() {
    const searchWrapper = this.root.querySelector(
      '.cmp-header__search.search_wrapper',
    ) as HTMLElement | null;

    if (!searchWrapper) return;

    const searchIcon = searchWrapper.querySelector(
      '.cmp-header__search-icon',
    ) as HTMLElement | null;

    const clearIcon = searchWrapper.querySelector(
      '.cmp-header__search-clear-icon',
    ) as HTMLElement | null;

    const searchInput = searchWrapper.querySelector(
      '.input_search_field',
    ) as HTMLInputElement | null;

    const suggestionsDropdown = searchWrapper.querySelector(
      '.cmp-site-search-suggestionsdropdown',
    ) as HTMLElement | null;

    if (searchIcon) {
      searchIcon.addEventListener('click', (event) => {
        event.preventDefault();
        performSearch(searchWrapper);
      });

      searchIcon.addEventListener(
        'keydown',
        (event: KeyboardEvent) => {
          if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            performSearch(searchWrapper);
          }
        },
      );

      searchIcon.addEventListener('focus', () => {
        searchWrapper.classList.add('input');
      });
    }

    searchWrapper.addEventListener('mouseover', () => {
      searchWrapper.classList.add('input');
      searchInput?.focus();
    });

    if (clearIcon && searchInput) {
      clearIcon.addEventListener('click', () => {
        searchInput.value = '';
      });
    }

    if (suggestionsDropdown) {
      suggestionsDropdown.addEventListener('click', (event) => {
        const item = (event.target as HTMLElement)?.closest('li');

        if (!item || !suggestionsDropdown.contains(item)) return;

        const term = item.textContent?.trim();

        if (!term) return;

        const input = searchWrapper.querySelector(
          '.input_search_field',
        ) as HTMLInputElement | null;

        if (!input) return;

        saveSearchTerm(term);

        input.value = term;

        performSearch(searchWrapper);
      });
    }
  }

  // ------------------------------------------------------------
  // Locale navigation
  // ------------------------------------------------------------
  private navigateToLocale(sLocale: string) {
    const regionCodeMap: Record<string, string> = {
      '/en-us/': 'US',
      '/en-in/': 'IN',
      '/en-au/': 'AU',
      '/en-hk/': 'HK',
      '/en-sg/': 'SG',
      '/ja-jp/': 'JP',
    };

    const matchedPrefix = Object.keys(regionCodeMap).find(
      (prefix) =>
        sLocale.startsWith(prefix) ||
        sLocale === prefix.slice(0, -1),
    );

    if (matchedPrefix) {
      const regionCode = regionCodeMap[matchedPrefix];

      document.cookie =
        `lumen_region_override=${regionCode}; ` +
        'path=/; ' +
        'Max-Age=600; ' +
        'SameSite=Lax; ' +
        'Secure';
    }

    if (/^(http|https):\/\//.test(sLocale)) {
      document.location.href = sLocale;
      return;
    }

    const newPath =
      sLocale +
      document.location.pathname.substring(
        document.location.pathname.substring(1).indexOf('/') + 2,
      );

    document.location.href =
      document.location.protocol +
      '//' +
      document.location.host +
      newPath +
      document.location.search;
  }

  // ------------------------------------------------------------
  // Account position
  // ------------------------------------------------------------
  private syncAccountBlockPosition() {
    const source = this.root.querySelector(
      '.header-nav__account--source',
    ) as HTMLElement | null;

    const slot = this.root.querySelector(
      '.header-nav__account--mobile-slot',
    ) as HTMLElement | null;

    const desktopWrapperEl = this.root.querySelector(
      '.header-nav__utility-wrapper--desktop',
    ) as HTMLElement | null;

    if (!source || !slot || !desktopWrapperEl) return;

    const isMobile = window.matchMedia(
      '(max-width: 1199px)',
    ).matches;

    if (isMobile && source.parentElement !== slot) {
      slot.appendChild(source);
    } else if (
      !isMobile &&
      source.parentElement !== desktopWrapperEl
    ) {
      desktopWrapperEl.appendChild(source);
    }
  }

  // ------------------------------------------------------------
  // Chat UI position
  // ------------------------------------------------------------
  private syncChatUIPosition() {
    const chatEl = this.root.querySelector(
      '#chat-ui',
    ) as HTMLElement | null;

    const mobileSlot = this.root.querySelector(
      '.header-nav__chat-mobile',
    ) as HTMLElement | null;

    const desktopSlot = this.root.querySelector(
      '.cmp-header__chatui--desktop-slot',
    ) as HTMLElement | null;

    if (!chatEl || !mobileSlot || !desktopSlot) return;

    const isMobile = window.matchMedia(
      '(max-width: 1199px)',
    ).matches;

    if (isMobile && chatEl.parentElement !== mobileSlot) {
      mobileSlot.appendChild(chatEl);
    } else if (
      !isMobile &&
      chatEl.parentElement !== desktopSlot
    ) {
      desktopSlot.appendChild(chatEl);
    }
  }

  // ------------------------------------------------------------
  // OneLink dropdown
  // ------------------------------------------------------------
  private bindOnelinkDropdownToggle() {
    this.root
      .querySelectorAll(
        '.cmp-onelinknav__close, .cmp-onelinknav-v2__close',
      )
      .forEach((closeEl) => {
        closeEl.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();

          this.closeAllOnelinkPopovers(this.root);
        });
      });

    this.root
      .querySelectorAll(
        '.cmp-onelinknav__toggle, .cmp-onelinknav-v2__toggle',
      )
      .forEach((toggleEl) => {
        toggleEl.addEventListener('click', (event) => {
          const target = event.target as HTMLElement;

          if (
            target.closest(
              '.cmp-onelinknav__lang-link, .cmp-onelinknav-v2__lang-link',
            )
          ) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();

          const instance = (toggleEl as HTMLElement).closest(
            '.cmp-onelinknav, .cmp-onelinknav-v2',
          ) as HTMLElement | null;

          const popover = instance?.querySelector(
            '.cmp-onelinknav__popover, .cmp-onelinknav-v2__popover',
          ) as HTMLElement | null;

          const triggerSpan = this.getDropdownArrow(
            toggleEl as HTMLElement,
          );

          if (!popover || !triggerSpan) return;

          const shouldOpen =
            !popover.classList.contains('active');

          this.closeAllOnelinkPopovers(this.root);

          if (shouldOpen) {
            popover.classList.add('active');
            triggerSpan.classList.add('active');
            triggerSpan.setAttribute(
              'aria-expanded',
              'true',
            );
          }
        });
      });
  }

  // ------------------------------------------------------------
  // Region label
  // ------------------------------------------------------------
  private injectRegionLabel(
    toggleEl: HTMLElement,
    regionText: string,
  ) {
    let label = toggleEl.querySelector(
      '.cmp-onelinknav__current-region-label, .cmp-onelinknav-v2__current-region-label',
    ) as HTMLElement | null;

    if (!label) {
      label = document.createElement('span');

      label.className =
        'cmp-onelinknav__current-region-label ' +
        'cmp-onelinknav-v2__current-region-label';

      const chevron = this.getDropdownArrow(toggleEl);

      if (chevron) {
        toggleEl.insertBefore(label, chevron);
      } else {
        toggleEl.appendChild(label);
      }
    }

    label.textContent = regionText;
  }

  // ------------------------------------------------------------
  // Region flag
  // ------------------------------------------------------------
  private injectRegionFlag(
    toggleEl: HTMLElement,
    flagSrc: string,
  ) {
    let flag = toggleEl.querySelector(
      'img.flag-icon, img.cmp-onelinknav-v2__current-region',
    ) as HTMLImageElement | null;

    if (!flag) {
      flag = document.createElement('img');

      flag.className =
        'flag-icon cmp-onelinknav-v2__current-region';

      flag.alt = 'Selected region flag';

      const label = toggleEl.querySelector(
        '.cmp-onelinknav__current-region-label, .cmp-onelinknav-v2__current-region-label',
      );

      const chevron = this.getDropdownArrow(toggleEl);

      const target = label ?? chevron;

      if (target) {
        toggleEl.insertBefore(flag, target);
      } else {
        toggleEl.appendChild(flag);
      }
    }

    flag.src = flagSrc;
  }

  // ------------------------------------------------------------
  // Dropdown arrow
  // ------------------------------------------------------------
  private getDropdownArrow(
    toggleEl: HTMLElement,
  ): HTMLElement | null {
    const dedicatedArrow = toggleEl.querySelector(
      '.cmp-onelinknav-v2__dropdown-arrow, .cmp-onelinknav__dropdown-arrow',
    ) as HTMLElement | null;

    if (dedicatedArrow) return dedicatedArrow;

    const spanChildren = Array.from(
      toggleEl.children,
    ).filter(
      (child): child is HTMLElement =>
        child instanceof HTMLElement &&
        child.tagName === 'SPAN',
    );

    return spanChildren[spanChildren.length - 1] ?? null;
  }

  // ------------------------------------------------------------
  // Close OneLink popovers
  // ------------------------------------------------------------
  private closeAllOnelinkPopovers(
    scope: HTMLElement,
  ) {
    scope
      .querySelectorAll(
        '.cmp-onelinknav__popover.active, .cmp-onelinknav-v2__popover.active',
      )
      .forEach((el) => {
        el.classList.remove('active');
      });

    scope
      .querySelectorAll(
        '.cmp-onelinknav__dropdown-arrow.active, ' +
          '.cmp-onelinknav-v2__dropdown-arrow.active',
      )
      .forEach((el) => {
        el.classList.remove('active');
        el.setAttribute('aria-expanded', 'false');
      });
  }
}

// ------------------------------------------------------------
// Initialize headers
// ------------------------------------------------------------
function initHeaders() {
  document
    .querySelectorAll('.cmp-header-navigation')
    .forEach((root) => {
      new HeaderNavigation(root as HTMLElement);
    });
}

if (document.readyState === 'loading') {
  document.addEventListener(
    'DOMContentLoaded',
    initHeaders,
  );
} else {
  initHeaders();
}
