from langchain.vectorstores import FAISS
from langchain.embeddings.openai import OpenAIEmbeddings
import pandas as pd

# Load dataset
df = pd.read_csv("/mnt/data/ipc_sections.csv")

# Combine section number & description into one text
df["content"] = df["Section"].astype(str) + " - " + df["Description"]
documents = df["content"].tolist()

# Convert documents into embeddings
embeddings = OpenAIEmbeddings()  # Use OpenAI API for embeddings
vectorstore = FAISS.from_texts(documents, embeddings)

# Save vector database
vectorstore.save_local("ipc_vector_db")

print("Vector Store Created Successfully!")
