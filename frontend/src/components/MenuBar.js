import React, { useState } from "react";
import { Link } from "react-router-dom";
import "../styles/MenuBar.css";

const MenuBar = () => {
    const [dropdownOpen, setDropdownOpen] = useState(false);

    const toggleDropdown = () => {
        setDropdownOpen(!dropdownOpen);
    };

    return (
        <div className="menu-bar">
            <div className="menu-logo">Prompt Writing Project</div>
            <nav className="menu-nav">
                <ul className="menu-list">
                    <li className="menu-item">
                        <Link to="/home">Home</Link>
                    </li>
                    <li className="menu-item">
                        <div
                            className="dropdown"
                            onMouseEnter={toggleDropdown}
                            onMouseLeave={toggleDropdown}
                        >
                            <span className="dropdown-title">Features</span>
                            {dropdownOpen && (
                                <ul className="dropdown-menu">
                                    <li>
                                        <Link to="/folder">Folders</Link>
                                    </li>
                                    <li>
                                        <Link to="/notes">Notes</Link>
                                    </li>
                                    <li>
                                        <Link to="/prompts">Prompts</Link>
                                    </li>
                                    <li>
                                        <Link to="/categories">Categories</Link>
                                    </li>
                                </ul>
                            )}
                        </div>
                    </li>
                    <li className="menu-item">
                        <Link to="/about">About</Link>
                    </li>
                    <li className="menu-item">
                        <Link to="/contact">Contact</Link>
                    </li>
                </ul>
            </nav>
        </div>
    );
};

export default MenuBar;