import pandas as pd

# Load the CSV file
df = pd.read_csv("/mnt/data/ipc_sections.csv")

# Convert to JSON format
df.to_json("ipc_sections.json", orient="records", indent=4)

print("Conversion Complete: ipc_sections.json Created!")
