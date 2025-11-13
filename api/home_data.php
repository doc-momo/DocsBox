<?php
/**
 * 首页数据接口
 * 为Home.vue提供页面列表和最近文档数据
 */

// 设置响应头
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 引入公共工具函数库
require_once 'utils.php';


// 获取数据库连接
function getDbConnection() {
    try {
        // 连接数据库
        $conn = connectToDatabase();
        if (!$conn) {
            throw new Exception("数据库连接失败");
        }
        
        return $conn;
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// 获取所有页面数据
function getAllPages($conn) {
    $sql = "SELECT id, name, description, iconUrl, logoUrl, created_at FROM pages ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $pages = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $pages[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'iconUrl' => $row['iconUrl'],
                'logoUrl' => $row['logoUrl'],
                'createdAt' => $row['created_at']
            ];
        }
    }
    
    return $pages;
}

// 获取文档总数
function getDocumentsCount($conn) {
    $sql = "SELECT COUNT(*) as count FROM documents";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }
    
    return 0;
}

// 获取页面总数
function getPagesCount($conn) {
    $sql = "SELECT COUNT(*) as count FROM pages";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }
    
    return 0;
}

// 获取最近5篇文档
function getRecentDocuments($conn) {
    $sql = "SELECT d.id, d.name, d.updated_at, p.name as pageName 
            FROM documents d 
            JOIN pages p ON d.pageId = p.id 
            ORDER BY d.updated_at DESC 
            LIMIT 5";
    
    $result = $conn->query($sql);
    $docs = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $docs[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'pageName' => $row['pageName'],
                'updatedAt' => $row['updated_at']
            ];
        }
    }
    
    return $docs;
}

// 处理API请求
try {
    $conn = getDbConnection();
    
    // 根据请求方法和参数返回不同数据
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    if ($requestMethod === 'GET' || $requestMethod === 'POST') {
        // 获取所有页面
        $pages = getAllPages($conn);
        
        // 获取统计信息
        $pagesCount = getPagesCount($conn);
        $docsCount = getDocumentsCount($conn);
        
        // 获取最近文档
        $recentDocs = getRecentDocuments($conn);
        
        // 返回数据
        echo json_encode([
            'status' => 'success',
            'data' => [
                'pages' => $pages,
                'pagesCount' => $pagesCount,
                'docsCount' => $docsCount,
                'recentDocs' => $recentDocs
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '不支持的请求方法'
        ]);
    }
    
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}