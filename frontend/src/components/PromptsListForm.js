import axios from "axios";
import React, { useEffect, useState } from "react";
import {useNavigate} from "react-router-dom";

const FetchPrompts = ({ onFetch }) => {
    useEffect(() => {
        const fetchPrompts = async () => {
            try {
                const response = await axios.get("http://localhost:8083/api/prompt/list");

                const promptsArray = Object.entries(response.data)
                    .filter(([key, value]) => !isNaN(key))
                    .map(([_, item]) => {
                        const hasValidNotes = validateNotes(item.notes); // Validate notes
                        return {
                            id: item.id,
                            title: item.title,
                            category: item.category.title,
                            notes: hasValidNotes
                                ? item.notes.map((note) => ({
                                    id: note.id,
                                    title: note.title,
                                    content: note.content,
                                    createdAt: note.createdAt,
                                    updatedAt: note.updatedAt,
                                }))
                                : [],
                        };
                    });

                onFetch(promptsArray);
            } catch (error) {
                console.error("Error fetching prompts:", error);
            }
        };
        fetchPrompts();
    }, []);

    return null;
};
const validateNotes = (notes) => {
    if (!Array.isArray(notes)) return false;
    return notes.every((note) =>
        note.id &&
        note.title &&
        note.content &&
        note.createdAt &&
        note.updatedAt
    );
};

const PromptsListForm = () => {
    const navigate = useNavigate();
    const [prompts, setPrompts] = useState([]);
    const [expandedPrompt, setExpandedPrompt] = useState(null);
    const togglePromptExpansion = (promptId) => {
        setExpandedPrompt((prev) => (prev === promptId ? null : promptId));
    };

    return (
        <div>
            <h1>Prompts</h1>
            <button onClick={() => navigate("/create/prompt")}>Create Prompt</button>
            <FetchPrompts onFetch={setPrompts} />
            <div style={styles.gridContainer}>
                {prompts.length > 0 ? (
                    prompts.map((prompt) => (
                        <div
                            key={prompt.id}
                            style={styles.promptCard}
                            onClick={() => togglePromptExpansion(prompt.id)}
                        >
                            <h2 style={styles.promptTitle}>{prompt.title}</h2>
                            <p style={styles.promptCategory}>Category: {prompt.category}</p>
                            {expandedPrompt === prompt.id ? (
                                <div>
                                    <p style={styles.promptNotesTitle}>Notes:</p>
                                    <ul style={styles.notesList}>
                                        {prompt.notes.map((note) => (
                                            <li key={note.id} style={styles.noteItem}>
                                                <h3>{note.title}</h3>
                                                <p>{note.content}</p>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ) : (
                                <button style={styles.expandButton}>+</button>
                            )}
                        </div>
                    ))
                ) : (
                    <p>Loading prompts...</p>
                )}
            </div>
        </div>
    );
};

const styles = {
    gridContainer: {
        display: "grid",
        gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))",
        gap: "16px",
        padding: "16px",
    },
    promptCard: {
        backgroundColor: "#f9f9f9",
        border: "1px solid #ddd",
        borderRadius: "8px",
        padding: "16px",
        cursor: "pointer",
        transition: "box-shadow 0.2s",
        boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)",
    },
    promptTitle: {
        fontSize: "1.25rem",
        margin: "0 0 8px 0",
    },
    promptCategory: {
        fontSize: "0.875rem",
        fontStyle: "italic",
        marginBottom: "8px",
    },
    promptNotesTitle: {
        fontSize: "1rem",
        fontWeight: "bold",
        marginTop: "16px",
        marginBottom: "8px",
    },
    notesList: {
        listStyleType: "none",
        padding: "0",
        margin: "0",
    },
    noteItem: {
        backgroundColor: "#f0f0f0",
        border: "1px solid #ccc",
        borderRadius: "4px",
        padding: "12px",
        marginBottom: "8px",
        wordWrap: "break-word",
        overflowWrap: "break-word",
    },

    expandButton: {
        backgroundColor: "#007bff",
        color: "#fff",
        border: "none",
        borderRadius: "50%",
        width: "30px",
        height: "30px",
        cursor: "pointer",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        fontWeight: "bold",
        fontSize: "1rem",
        boxShadow: "0px 2px 4px rgba(0, 0, 0, 0.2)",
    },
};

export default PromptsListForm;