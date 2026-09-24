import json
import re

with open('resources/views/welcome.blade.php', 'r') as f:
    html = f.read()

# Extract colors
m = re.search(r'tailwind\.config=\{.*?\"colors\":(\{.*?\}),\"borderRadius\"', html)
if not m:
    print("Colors not found!")
    exit(1)

colors_json = m.group(1)
colors = json.loads(colors_json)

# Create variables mapping
tailwind_colors = {}
root_vars = []
dark_vars = []

# Basic dark mode mapping logic
dark_map = {
    "background": "#0b0e14",
    "surface": "#0b0e14",
    "surface-bright": "#1a1d24",
    "surface-dim": "#080a0f",
    "surface-container-lowest": "#05070a",
    "surface-container-low": "#11151c",
    "surface-container": "#181c25",
    "surface-container-high": "#232833",
    "surface-container-highest": "#333945",
    "on-background": "#e2e4eb",
    "on-surface": "#e2e4eb",
    "on-surface-variant": "#c4c6d0",
    "outline": "#8e9099",
    "outline-variant": "#44474f",
    "primary": "#bac3ff",
    "on-primary": "#00105b",
    "primary-container": "#143ee4",
    "on-primary-container": "#dee0ff",
    "secondary": "#bec6e0",
    "on-secondary": "#131b2e",
    "secondary-container": "#3f465c",
    "on-secondary-container": "#dae2fd",
    "tertiary": "#4cd7f6",
    "on-tertiary": "#001f26",
    "tertiary-container": "#004e5c",
    "on-tertiary-container": "#acedff",
    "error": "#ffb4ab",
    "on-error": "#690005",
    "error-container": "#93000a",
    "on-error-container": "#ffdad6",
    "inverse-surface": "#e2e4eb",
    "inverse-on-surface": "#0b0e14",
    "inverse-primary": "#143ee4",
}

for key, val in colors.items():
    var_name = f"--color-{key}"
    tailwind_colors[key] = f"var({var_name})"
    root_vars.append(f"  {var_name}: {val};")
    dark_val = dark_map.get(key, val)
    dark_vars.append(f"  {var_name}: {dark_val};")

# Update tailwind config
new_colors_json = json.dumps(tailwind_colors)
html = html.replace(f'\"colors\":{colors_json}', f'\"colors\":{new_colors_json}')

# Insert styles
style_block = "\n<style>\n:root {\n" + "\n".join(root_vars) + "\n}\n.dark {\n" + "\n".join(dark_vars) + "\n}\n</style>\n</head>"
html = html.replace("</head>", style_block)

with open('resources/views/welcome.blade.php', 'w') as f:
    f.write(html)

print("Updated welcome.blade.php with dark mode variables.")
