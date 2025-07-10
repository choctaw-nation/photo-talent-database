# About this Package

[Animate.css](https://animate.style/) is an excellent 3rd-party library, and we **highly recommend** using a [custom build](https://animate.style/#custom-builds) so you can remove bloat.

There are a lot of animations that are, in KJ's opinion, obnoxious. So, you can comment them out, bundle your own version, and create a much smaller, focused css file.

## Quick Start to build custom animate css

1. Install the project

```
git clone https://github.com/animate-css/animate.css.git
cd animate.css
npm install
```

2. Comment out what you _don't_ need
3. Run `npm start`
4. Copy the new `animate(.min/.compat).css` files into `css-animations`
5. Delete everything that was downloaded
