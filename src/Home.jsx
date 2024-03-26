// Home.jsx

import React, { useEffect, useState } from "react";
import { Navigate, useNavigate } from "react-router-dom";
import axios from "axios";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const Home = () => {
  const navigate = useNavigate(); // Initialize useNavigate hook
  const [userData, setUserData] = useState(null);
  const [profilePicture, setProfilePicture] = useState(null);

  const handleLogout = () => {
    // Clear user information from local storage
    localStorage.removeItem("loggedInUser");
    localStorage.removeItem("userId");

    navigate("/login");
  };

  const handleFileChange = (e) => {
    setProfilePicture(e.target.files[0]);
  };

  const handleUpload = async () => {
    try {
      const userId = JSON.parse(localStorage.getItem("userId"));
      const formData = new FormData();
      formData.append("profilePicture", profilePicture);
      formData.append("userId", userId);
      const response = await axios.post(
        "http://localhost:8000/uploadProfilePicture.php",
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );
      console.log(response.data);
      console.log(response.data);
      toast.success("Profile picture uploaded successfully");
      // Fetch user details after uploading profile picture
      getUserDetails();
    } catch (error) {
      console.error("Error uploading profile picture:", error);
      toast.error(error.response.data.message);
    }
  };

  const getUserDetails = async () => {
    try {
      const userId = JSON.parse(localStorage.getItem("userId"));
      if (userId) {
        const response = await axios.get(
          `http://localhost:8000/userDetail.php?id=${userId}`
        );
        setUserData(response.data.data);
      } else {
        navigate("/login"); // Redirect to login page if no userId found
      }
    } catch (error) {
      console.error("Error fetching user details:", error);
      navigate("/login"); // Redirect to login page if an error occurs
    }
  };

  useEffect(() => {
    getUserDetails();
  }, []);

  return (
    <>
      <div className="container mt-5">
        <div className="card">
          <div className="card-body">
            {userData && (
              <>
                <div className="row justify-content-end">
                  <div className="col-sm-12 text-end">
                    <button
                      className="btn btn-danger mt-3"
                      onClick={handleLogout}
                    >
                      Logout
                    </button>
                  </div>
                </div>
                <h2 className="card-title">Welcome, {userData.username}</h2>
                <p className="card-text">Email: {userData.email}</p>
                {userData.profile_picture && (
                  <div className="mb-3">
                    <img
                      src={`http://localhost:8000/${userData.profile_picture}`}
                      alt="Profile"
                      className="img-fluid"
                      style={{ width: 200, height: 200 }}
                    />
                  </div>
                )}
                <div className="form-group">
                  <input
                    type="file"
                    className="form-control-file"
                    onChange={handleFileChange}
                  />
                  <button
                    className="btn btn-primary mt-2"
                    onClick={handleUpload}
                  >
                    Upload Profile Picture
                  </button>
                </div>
              </>
            )}
          </div>
        </div>
        <ToastContainer />
      </div>
    </>
  );
};

export default Home;
