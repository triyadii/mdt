import re
import os

with open('resources/views/welcome.blade.php', 'r', encoding='utf-8') as f:
    html = f.read()

# Header: everything up to <main
header_match = re.search(r'(.*?)<main', html, re.DOTALL)
header = header_match.group(1) if header_match else ''

# Footer: everything from </main> to the end
footer_match = re.search(r'(</main>.*)', html, re.DOTALL)
footer = footer_match.group(1) if footer_match else ''

# Replace links in header/footer to use route('home')
header = re.sub(r'href="#', r'href="{{ route(\'home\') }}#', header)
footer = re.sub(r'href="#', r'href="{{ route(\'home\') }}#', footer)
# Fix href="{{ route('home') }}#" which might happen if there was already #
header = header.replace('href="{{ route(\'home\') }}#news"', 'href="{{ route(\'front.berita\') }}"')
footer = footer.replace('href="{{ route(\'home\') }}#news"', 'href="{{ route(\'front.berita\') }}"')

layout_content = f"""{header}
<main class="w-full pt-20 bg-surface">
    @yield('content')
{footer}"""

os.makedirs('resources/views/layouts', exist_ok=True)
with open('resources/views/layouts/front.blade.php', 'w', encoding='utf-8') as f:
    f.write(layout_content)

print("Layout extracted successfully.")
